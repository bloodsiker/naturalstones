<?php

namespace BookBundle\Helper;

use BookBundle\Entity\Book;
use BookBundle\Entity\BookInfoView;
use Doctrine\ORM\EntityManager;

/**
 * Class BookViewHelper
 */
class BookViewHelper
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ArticleExtension constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {

        $this->entityManager = $entityManager;
    }

    /**
     * @param Book $book
     *
     * @return bool
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function doView(Book $book)
    {
        $now = new \DateTime('now');
        $viewBook = $this->getBookInfoView($book);

        if ($viewBook) {
            $viewBook->doView();

            $this->entityManager->persist($viewBook);
            $this->entityManager->flush();
        } else {
            $viewBook = new BookInfoView();
            $viewBook->doView();
            $viewBook->setBook($book);
            $viewBook->setViewAt($now);

            $this->entityManager->persist($viewBook);
            $this->entityManager->flush();
        }

        return true;
    }

    /**
     * @param Book $book
     *
     * @return bool
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function doDownload(Book $book)
    {
        $viewBook = $this->getBookInfoView($book);

        if ($viewBook) {
            $viewBook->doDownload();

            $this->entityManager->persist($viewBook);
            $this->entityManager->flush();
        }

        return true;
    }

    /**
     * @param Book $book
     *
     * @return BookInfoView|object|null
     *
     * @throws \Exception
     */
    protected function getBookInfoView(Book $book)
    {
        $repository = $this->entityManager->getRepository(BookInfoView::class);

        $now = new \DateTime('now');
        return $repository->findOneBy(['book' => $book, 'viewAt' => $now]);
    }
}
