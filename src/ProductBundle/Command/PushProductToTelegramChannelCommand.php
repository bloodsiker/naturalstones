<?php

namespace ProductBundle\Command;

use AppBundle\Services\SendTelegramService;
use Doctrine\ORM\EntityManagerInterface;
use ProductBundle\Entity\Product;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PushProductToTelegramChannelCommand extends Command
{
    protected static $defaultName = 'product:push-to-telegram';

    private EntityManagerInterface $em;
    private SendTelegramService $telegramService;

    public function __construct(
        EntityManagerInterface $em,
        SendTelegramService $telegramService
    ) {
        parent::__construct();
        $this->em = $em;
        $this->telegramService = $telegramService;
    }

    protected function configure(): void
    {
        $this->setDescription('Push products to telegram channel');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $products = $this->em->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->where('p.isActive = 1')
            ->andWhere('p.telegramMessageId IS NULL')
            ->leftJoin('p.translations', 'pt')
            ->addSelect('pt')
            ->addOrderBy('p.id', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();

        foreach ($products as $product) {
            $this->telegramService->sendProductToChannel($product);
            $output->writeln('Product ID ' . $product->getId());
        }

        return Command::SUCCESS;
    }
}