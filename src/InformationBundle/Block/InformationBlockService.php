<?php

namespace InformationBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Doctrine\ORM\EntityManagerInterface;
use InformationBundle\Entity\Information;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class InformationBlockService extends AbstractEditableBlockService
{
    public function __construct(
        Environment $twig,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => '@Information/Block/modal_info.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $now = (new \DateTime('now'))->getTimestamp();
        $informations = $this->em->getRepository(Information::class)
            ->findBy(['isActive' => true], ['id' => 'ASC']);

        $listInformations = [];
        $ids = [];
        foreach ($informations as $information) {
            if ($this->isInTimeWindow($information, $now)) {
                $listInformations[] = $information;
                $ids[] = $information->getId();
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'informations' => $listInformations,
            'ids' => implode(',', $ids),
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }

    private function isInTimeWindow(Information $information, int $now): bool
    {
        $start = $information->getStartedAt()?->getTimestamp();
        $end = $information->getFinishedAt()?->getTimestamp();

        if (null === $start && null === $end) {
            return false;
        }

        if (null !== $start && $start >= $now) {
            return false;
        }

        if (null !== $end && $end <= $now) {
            return false;
        }

        return true;
    }
}