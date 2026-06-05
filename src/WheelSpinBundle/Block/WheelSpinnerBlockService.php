<?php

namespace WheelSpinBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use AppBundle\Services\SendTelegramService;
use Doctrine\ORM\EntityManagerInterface;
use OrderBundle\Entity\Order;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;
use WheelSpinBundle\Entity\WheelSpin;
use WheelSpinBundle\Entity\WheelSpinHasOption;

class WheelSpinnerBlockService extends AbstractEditableBlockService
{
    public const DEFAULT_TEMPLATE = '@WheelSpin/Block/wheel_spinner.html.twig';

    public function __construct(
        Environment $twig,
        private readonly EntityManagerInterface $em,
        private readonly RequestStack $request,
        private readonly SendTelegramService $telegramService,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'order' => null,
            'success_page' => false,
            'template' => self::DEFAULT_TEMPLATE,
        ]);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();
        $order = $blockContext->getSetting('order');

        if (!$order && $request->isXmlHttpRequest()) {
            $order = $this->em->getRepository(Order::class)->find($request->get('order_id'));
        }

        if (!$order || !$order->getId()) {
            return new Response();
        }

        $wheelSpin = $this->em->getRepository(WheelSpin::class)->getWheelSpin($order->getOrderSum());
        if (!$wheelSpin) {
            return new Response();
        }

        $spinValues = $this->getSections($wheelSpin);
        $spinValuesJson = $this->getSectionsJson($spinValues);

        if ($request->isXmlHttpRequest()) {
            return $this->handleSpinRequest($order, $wheelSpin, $spinValuesJson);
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'order' => $order,
            'spinValuesJson' => json_encode(array_values($spinValuesJson)),
            'spinValues' => json_encode(array_values($spinValues)),
            'spinValuesTable' => $wheelSpin,
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getSections(WheelSpin $wheelSpin): array
    {
        $result = [];
        $i = 0;

        /** @var WheelSpinHasOption $option */
        foreach ($wheelSpin->getWheelSpinHasOption() as $option) {
            $result[$i] = [
                'degrees' => $option->getDegrees(),
                'id' => $option->getId(),
                'option_id' => $option->getWheelSpinOption()->getId(),
                'value' => $option->getWheelSpinOption()->getPrizeName(),
                'label' => $option->getLabel(),
                'color' => sprintf('#%s', $option->getColour()),
                'size' => 10,
            ];
            ++$i;
        }

        uasort($result, static fn ($a, $b) => $a['label'] <=> $b['label']);

        return $result;
    }

    /**
     * @param list<array<string, mixed>> $spinValues
     *
     * @return list<array<string, mixed>>
     */
    public function getSectionsJson(array $spinValues): array
    {
        $result = [];
        $i = 0;

        foreach ($spinValues as $option) {
            foreach (explode(',', (string) $option['degrees']) as $degree) {
                $degree = trim($degree);
                if ('' === $degree || !str_contains($degree, ':')) {
                    continue;
                }

                [$minDegree, $maxDegree] = array_pad(explode(':', $degree, 2), 2, null);
                if (null === $minDegree || null === $maxDegree) {
                    continue;
                }

                $result[$i] = [
                    'minDegree' => $minDegree,
                    'maxDegree' => $maxDegree,
                    'id' => $option['id'],
                    'value' => $option['value'],
                    'label' => $option['label'],
                    'color' => $option['color'],
                    'size' => $option['size'],
                ];
                ++$i;
            }
        }

        uasort($result, static fn ($a, $b) => $a['label'] <=> $b['label']);

        return $result;
    }

    /**
     * @param list<array<string, mixed>> $spinValuesJson
     */
    private function handleSpinRequest(Order $order, WheelSpin $wheelSpin, array $spinValuesJson): JsonResponse
    {
        if ($order->getIsSpin()) {
            return new JsonResponse(['error' => 'already_spun'], Response::HTTP_CONFLICT);
        }

        $data = $this->buildSpinOptionData($wheelSpin);
        $winningId = $this->getRandomIndex($data);

        $win = $this->findWinSection($spinValuesJson, $winningId);
        $spinOption = $this->findSpinOption($data, $winningId);

        try {
            if (!$this->persistSpin($order, $spinOption)) {
                return new JsonResponse(['error' => 'already_spun'], Response::HTTP_CONFLICT);
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        try {
            $this->telegramService->editOrderTelegramMessage($order);
        } catch (\Throwable $e) {
            // Telegram update must not block the wheel flow.
        }

        $rand = $win
            ? random_int((int) $win['minDegree'], (int) $win['maxDegree'])
            : random_int(1, 359);

        return new JsonResponse(['spin' => $rand]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildSpinOptionData(WheelSpin $wheelSpin): array
    {
        $data = [];
        $i = 0;

        /** @var WheelSpinHasOption $option */
        foreach ($wheelSpin->getWheelSpinHasOption() as $option) {
            $data[$i] = [
                'spin_option' => $option->getWheelSpinOption(),
                'percent' => $option->getPercent(),
                'valuation' => $option->getValuation(),
                'id' => $option->getId(),
            ];
            ++$i;
        }

        return $data;
    }

    /**
     * @param list<array<string, mixed>> $spinValuesJson
     *
     * @return array<string, mixed>|null
     */
    private function findWinSection(array $spinValuesJson, mixed $winningId): ?array
    {
        foreach ($spinValuesJson as $spin) {
            if ($spin['id'] == $winningId) {
                return $spin;
            }
        }

        return null;
    }

    /**
     * @param list<array<string, mixed>> $data
     *
     * @return array<string, mixed>
     */
    private function findSpinOption(array $data, mixed $winningId): array
    {
        $key = array_search($winningId, array_column($data, 'id'));

        return $data[$key];
    }

    /**
     * @param array<string, mixed> $spinOption
     */
    private function persistSpin(Order $order, array $spinOption): bool
    {
        $connection = $this->em->getConnection();
        $connection->beginTransaction();

        try {
            if (!$this->em->getRepository(Order::class)->claimSpin($order)) {
                $connection->rollBack();

                return false;
            }

            $order->setIsSpin(true);
            $order->setWheelSpinOption($spinOption['spin_option']);
            $order->setSpinPrize($spinOption['spin_option']);
            $this->em->persist($order);
            $this->em->flush();
            $connection->commit();

            return true;
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            throw $e;
        }
    }

    /**
     * @param list<array<string, mixed>> $data
     */
    private function getRandomIndex(array $data, string $column = 'percent'): mixed
    {
        if (!$data) {
            return null;
        }

        $weights = array_map(
            static fn (array $item) => max(0, (int) round((float) ($item[$column] ?? 0.0) * 100)),
            $data
        );
        $total = array_sum($weights);

        if ($total <= 0 && 'valuation' !== $column) {
            return $this->getRandomIndex($data, 'valuation');
        }

        if ($total <= 0) {
            return $data[array_key_last($data)]['id'] ?? null;
        }

        $rand = random_int(1, $total);
        $current = 0;

        foreach ($data as $index => $item) {
            $current += $weights[$index] ?? 0;
            if ($rand <= $current) {
                return $item['id'];
            }
        }

        return $data[array_key_last($data)]['id'] ?? null;
    }
}