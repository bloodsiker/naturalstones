<?php

namespace InformationBundle\Block;

use Doctrine\ORM\EntityManager;
use InformationBundle\Entity\Information;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InformationBlockService
 */
class InformationBlockService extends AbstractAdminBlockService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * MainImageBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     */
    public function __construct($name, EngineInterface $templating, EntityManager $em)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
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
            'InformationBundle',
            ['class' => 'fa fa-code']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => 'InformationBundle:Block:modal_info.html.twig',
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $now = (new \DateTime('now'))->getTimestamp();

        $repository = $this->em->getRepository(Information::class);

        $informations = $repository->findBy(['isActive' => true], ['id' => 'ASC']);

        $listInformations = [];
        $ids = [];
        foreach ($informations as $information) {
            if ($information && $information->getStartedAt() && $information->getFinishedAt()) {
                if ($information->getStartedAt()->getTimestamp() < $now && $information->getFinishedAt()->getTimestamp() > $now) {
                    $listInformations[] = $information;
                    $ids[] = $information->getId();
                }
            } elseif ($information && $information->getStartedAt()) {
                if ($information->getStartedAt()->getTimestamp() < $now) {
                    $listInformations[] = $information;
                    $ids[] = $information->getId();
                }
            } elseif ($information && $information->getFinishedAt()) {
                if ($information->getFinishedAt()->getTimestamp() > $now) {
                    $listInformations[] = $information;
                    $ids[] = $information->getId();
                }
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'informations' => $listInformations,
            'ids'          => implode(',', $ids),
            'block'        => $block,
            'settings'     => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
