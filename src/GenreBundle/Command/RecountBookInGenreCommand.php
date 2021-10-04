<?php

namespace GenreBundle\Command;

use BookBundle\Entity\Book;
use GenreBundle\Entity\Genre;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RecountBookInGenreCommand
 */
class RecountBookInGenreCommand extends ContainerAwareCommand
{
    private $entityManager;

    /**
     * Configure console command arguments
     */
    protected function configure()
    {
        $this
            ->setName('genre:count:book')
            ->setDescription('Recount book in genres')
        ;
    }

    /**
     * Command execute
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
        $genreRepository = $this->entityManager->getRepository(Genre::class);

        $genreList = $genreRepository->createQueryBuilder('g')
            ->where('g.parent IS NOT NULL')
            ->getQuery()->getResult();

        $sum = 0;

        foreach ($genreList as $genre) {
            $count = $this->getCountBook($genre);
            $genre->setCountBook($count);
            $this->entityManager->persist($genre);
            $sum += $count;
            $output->writeln('Genre ID ' . $genre->getId() . ' count -> ' .$count);
        }
        $this->entityManager->flush();

        $genreList = $genreRepository->createQueryBuilder('g')
            ->where('g.parent IS NULL')
            ->getQuery()->getResult();

        foreach ($genreList as $genre) {
            $count = $this->getCountBook($genre);
            $genre->setCountBook($count);
            $this->entityManager->persist($genre);
            $output->writeln('Main Genre ID ' . $genre->getId() . ' count -> ' .$count);
        }
        $this->entityManager->flush();


        return true;
    }

    /**
     * @param $genre
     *
     * @return int
     */
    protected function getCountBook($genre): int
    {
        $repo = $this->entityManager->getRepository(Book::class);

        $qb = $repo->baseBookQueryBuilder();
        $countBook = $repo->filterByGenre($qb, $genre)
            ->distinct()
            ->resetDQLPart('select')
            ->addSelect('count(b) as count')
            ->getQuery()->getSingleResult();

        return $countBook['count'];
    }
}