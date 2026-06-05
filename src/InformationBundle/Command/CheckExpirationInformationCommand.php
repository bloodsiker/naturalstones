<?php

namespace InformationBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use InformationBundle\Entity\Information;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'information:check-expiration', description: 'Disable inactive informations')]
class CheckExpirationInformationCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
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