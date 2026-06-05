<?php

namespace ShareBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ShareBundle\Entity\Stone;
use ShareBundle\Entity\StoneRepository;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class StonesBlockService extends AbstractEditableBlockService
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
            'items_count' => 200,
            'is_main' => false,
            'show_letters' => false,
            'letter' => null,
            'zodiac' => null,
            'view_all' => false,
            'title' => null,
            'template' => '@Share/Block/stones.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();
        /** @var StoneRepository $repository */
        $repository = $this->doctrine->getRepository(Stone::class);

        $qb = $repository->baseStoneQueryBuilder();

        if ($blockContext->getSetting('is_main')) {
            $repository->filterByShowMain($qb);
        }
        if ($zodiac = $blockContext->getSetting('zodiac')) {
            $repository->filterByZodiac($qb, $zodiac);
        }
        if ($letter = $blockContext->getSetting('letter')) {
            $repository->filterByLetter($qb, $letter);
        }

        $stones = $qb->setFirstResult(0)
            ->setMaxResults((int) $blockContext->getSetting('items_count'))
            ->getQuery()
            ->getResult();

        $letterStones = [];
        if ($blockContext->getSetting('show_letters')) {
            foreach ($repository->uniqLetterByStone($request->getLocale()) as $value) {
                $letterStones[$value[1]] = [];
            }
            foreach ($stones as $stone) {
                $first = mb_substr($stone->getName(), 0, 1);
                if (array_key_exists($first, $letterStones)) {
                    $letterStones[$first][] = $stone;
                }
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'letters' => $letterStones,
            'stones' => $stones,
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}