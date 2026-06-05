<?php

namespace ShareBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ShareBundle\Entity\Stone;
use ShareBundle\Entity\StoneRepository;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class StoneConstructorBlockService extends AbstractEditableBlockService
{
    public function __construct(
        Environment $twig,
        protected readonly Registry $doctrine,
        private readonly RequestStack $request,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'show_letters' => false,
            'title' => null,
            'template' => '@Share/Block/stone_constructor.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();

        if ($request->isXmlHttpRequest()) {
            return $this->buildAjaxResponse((string) $request->get('size'));
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }

    private function buildAjaxResponse(string $size): JsonResponse
    {
        /** @var StoneRepository $repository */
        $repository = $this->doctrine->getRepository(Stone::class);
        $qb = $repository->baseStoneQueryBuilder();
        $repository->filterByShowConstructor($qb);
        $stones = $qb->getQuery()->getResult();

        $result = [];
        $stonesList = [];
        foreach ($stones as $stone) {
            foreach ($stone->getStoneHasConstructor() as $constructor) {
                if ($size !== $constructor->getSize()) {
                    continue;
                }
                $result[$stone->getSlug()][] = [
                    'id' => $constructor->getId(),
                    'name' => $stone->getName(),
                    'img' => $constructor->getImage()->getPath(),
                ];
                $stonesList[$stone->getId()] = [
                    'slug' => $stone->getSlug(),
                    'name' => $stone->getName(),
                ];
            }
        }

        return new JsonResponse([
            'stones' => $stonesList,
            'stoneType' => $result,
        ]);
    }
}