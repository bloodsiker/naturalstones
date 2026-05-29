<?php

namespace InformationBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use InformationBundle\Entity\Information;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckExpirationInformationCommand extends Command
{
    protected static $defaultName = 'information:check-expiration';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->setDescription('Disable inactive informations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $informationList = $this->em->getRepository(Information::class)
            ->createQueryBuilder('i')
            ->where('i.isActive = 1')
            ->andWhere('i.finishedAt < :now')
            ->setParameter('now', new \DateTime('now'))
            ->getQuery()
            ->getResult();

        foreach ($informationList as $information) {
            $information->setIsActive(false);
            $this->em->persist($information);
            $output->writeln('Information ID ' . $information->getId());
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}
