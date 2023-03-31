<?php

namespace ProductBundle\Command;

use AppBundle\Services\SendTelegramService;
use Doctrine\ORM\EntityManager;
use ProductBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PushProductToTelegramChannelCommand
 */
class PushProductToTelegramChannelCommand extends ContainerAwareCommand
{
    /**
     * Configure console command arguments
     */
    protected function configure()
    {
        $this
            ->setName('product:push-to-telegram')
            ->setDescription('Push products to telegram channel')
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

        /** @var SendTelegramService $telegramService */
        $telegramService = $this->getContainer()->get('app.send_telegram');

        $repository = $entityManager->getRepository(Product::class);

        $products = $repository->baseProductQueryBuilder()
            ->andWhere('p.telegramMessageId IS NULL')
            ->resetDQLPart('orderBy')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();

        foreach ($products as $product) {
            $telegramService->sendProductToChannel($product);
            $output->writeln('Product ID ' . $product->getId());
        }

        return true;
    }
}