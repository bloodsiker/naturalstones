<?php

namespace WheelSpinBundle\Block;

use Doctrine\ORM\EntityManager;
use OrderBundle\Entity\Order;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Meta\Metadata;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WheelSpinBundle\Entity\WheelSpin;
use WheelSpinBundle\Entity\WheelSpinHasOption;

/**
 * Class WheelSpinnerBlockService
 */
class WheelSpinnerBlockService extends AbstractAdminBlockService
{
    const DEFAULT_TEMPLATE = 'WheelSpinBundle:Block:wheel_spinner.html.twig';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     * @param RequestStack    $request
     */
    public function __construct($name, EngineInterface $templating, EntityManager $em, RequestStack $request)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
        $this->request = $request;
    }

    /**
     * @param null $code
     *
     * @return Metadata
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata(
            $this->getName(),
            (!is_null($code) ? $code : $this->getName()),
            false,
            'WheelSpinBundle',
            ['class' => 'fa fa-code']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'order' => null,
            'template' => self::DEFAULT_TEMPLATE,
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();

        $order = $blockContext->getSetting('order');

        if (!$order && $request->isXmlHttpRequest()) {
            $orderRepository = $this->em->getRepository(Order::class);
            $order = $orderRepository->find($request->get('order_id'));
        }

        if (!$order->getId()) {
            return new Response();
        }

        $repository = $this->em->getRepository(WheelSpin::class);
        $wheelSpin = $repository->getWheelSpin($order->getOrderSum());

        if (!$wheelSpin) {
            return new Response();
        }

        $spinValues = $this->getSections($wheelSpin);
        $spinValuesJson = $this->getSectionsJson($spinValues);

        if ($request->isXmlHttpRequest()) {

            if ($order->getIsSpin() || $order->getStatus() !== Order::STATUS_COMPLETED) {
                return;
            }

            $data = [];
            /** @var WheelSpinHasOption $option */
            $i = 0;
            foreach ($wheelSpin->getWheelSpinHasOption() as $option) {
                $data[$i]['spin_option'] = $option->getWheelSpinOption();
                $data[$i]['valuation'] = $option->getValuation();
                $data[$i]['id'] = $option->getId();
                $i++;
            }

//            $this->testSpins($data);

            $id = $this->getRandomIndex($data);

            $win = [];
            foreach ($spinValuesJson as $spin) {
                if ($spin['id'] == $id) {
                    $win = $spin;
                    break;
                }
            }

            $spinOptionKey = array_search($id, array_column($data, 'id'));
            $spinOption = $data[$spinOptionKey];

            $order->setIsSpin(true);
            $order->setWheelSpinOption($spinOption['spin_option']);
            $order->setSpinPrize($spinOption['spin_option']);
            $this->em->persist($order);
            $this->em->flush();

            if ($win) {
                $rand = random_int($win['minDegree'], $win['maxDegree']);
            } else {
                $rand = random_int(1, 359);
            }

            return new JsonResponse(['spin' => $rand]);
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'order'           => $order,
            'spinValuesJson'  => json_encode(array_values($spinValuesJson)),
            'spinValues'      => json_encode(array_values($spinValues)),
            'spinValuesTable' => $wheelSpin,
            'block'           => $block,
            'settings'        => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }

    public function getSections(WheelSpin $wheelSpin)
    {
        $result = [];

        $i = 0;
        /** @var WheelSpinHasOption $option */
        foreach ($wheelSpin->getWheelSpinHasOption() as $option) {
            $result[$i]['degrees'] = $option->getDegrees();
            $result[$i]['id'] = $option->getId();
            $result[$i]['option_id'] = $option->getWheelSpinOption()->getId();
            $result[$i]['value'] = $option->getWheelSpinOption()->getPrizeName();
            $result[$i]['label'] = $option->getLabel();
            $result[$i]['color'] = sprintf('#%s', $option->getColour());
            $result[$i]['size'] = 10;
            $i++;
        }

        uasort($result, static function ($a, $b) {
            return $a['label'] <=> $b['label'];
        });

        return $result;
    }

    public function getSectionsJson(array $spinValues)
    {
        $result = [];

        $i = 0;
        foreach ($spinValues as $option) {
            $explodeDegree = explode(',', $option['degrees']);
            foreach ($explodeDegree as $degree) {
                [$minDegree, $maxDegree] = explode(':', $degree);
                $result[$i]['minDegree'] = $minDegree;
                $result[$i]['maxDegree'] = $maxDegree;
                $result[$i]['id'] = $option['id'];
                $result[$i]['value'] = $option['value'];
                $result[$i]['label'] = $option['label'];
                $result[$i]['color'] = $option['color'];
                $result[$i]['size'] = $option['size'];
                $i++;
            }
        }

        uasort($result, static function ($a, $b) {
            return $a['label'] <=> $b['label'];
        });

        return $result;
    }

    private function getRandomIndex($data, $column = 'valuation') {
        $rand = random_int(1, array_sum(array_column($data, $column)));
        $cur = $prev = 0;
        for ($i = 0, $count = count($data); $i < $count; ++$i) {
            $prev += $i !== 0 ? $data[$i-1][$column] : 0;
            $cur += $data[$i][$column];
            if ($rand > $prev && $rand <= $cur) {
                return $data[$i]['id'];
            }
        }
        return $data[-1];
    }

    private function testSpins($data) {
        $test['a'] = 0;
        $test['b'] = 0;
        $test['c'] = 0;

        for ( $i = 0; $i < 5000; $i++ )
        {
            $x = $this->getRandomIndex($data);

            switch ( $x )
            {
                case 50: { $test['a']++; break; }
                case 3: { $test['b']++; break; }
                case 1: { $test['c']++; break; }
            }
        }

        dump($test);die;
    }
}
