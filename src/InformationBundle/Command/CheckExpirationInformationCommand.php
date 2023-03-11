<?php

namespace InformationBundle\Command;

use Doctrine\ORM\EntityManager;
use InformationBundle\Entity\Information;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckExpirationInformationCommand
 */
class CheckExpirationInformationCommand extends ContainerAwareCommand
{
    /**
     * Configure console command arguments
     */
    protected function configure()
    {
        $this
            ->setName('information:check-expiration')
            ->setDescription('Disable inactive informations')
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
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        $repository = $entityManager->getRepository(Information::class);

        $informationList = $repository->createQueryBuilder('i')
            ->where('i.isActive = 1')
            ->andWhere('i.finishedAt > :now')->setParameter('now', new \DateTime('now'))
            ->getQuery()->getResult();

        foreach ($informationList as $information) {
            $information->setIsActive(false);
            $entityManager->persist($information);
            $output->writeln('Information ID ' . $information->getId());
        }
        $entityManager->flush();

        return true;
    }
}