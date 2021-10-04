<?php

namespace AppBundle\Command;

use BookBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BookCommand
 */
class BookCommand extends ContainerAwareCommand
{
    private $entityManager;
    private $output;
    private $bookRepository;

    /**
     * Configure console command arguments
     */
    protected function configure()
    {
        $this
            ->setName('book:view')
            ->setDescription('Inc view and download book')
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
        $this->output = $output;

        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->bookRepository = $this->getContainer()->get('doctrine')->getRepository(Book::class);

        $listBook = $this->bookRepository->findAll();
        foreach ($listBook as $book) {
            $views = $book->getViews() + mt_rand(10, 20);
            $book->setViews($views);
            $download = $book->getDownload() + mt_rand(1, 4);
            $book->setDownload($download);
            $rand = mt_rand(0, 100);
            $countP = $book->getRatePlus() + 1;
            $countM = $book->getRateMinus() + 1;
            if ($rand > 20 && $rand < 80) {
                $book->setRatePlus($countP);
            } elseif ($rand <= 20) {
                $book->setRateMinus($countM);
            }
            $output->writeln('Book--> Download = '.$download.' Views = '.$views.' P = '.$book->getRatePlus().' M = '.$book->getRateMinus());

            $this->entityManager->persist($book);
        }
        $this->entityManager->flush();

        return true;
    }
}