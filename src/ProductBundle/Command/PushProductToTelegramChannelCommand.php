<?php

namespace ProductBundle\Command;

use AppBundle\Services\SendTelegramService;
use Doctrine\ORM\EntityManagerInterface;
use ProductBundle\Entity\Product;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'product:push-to-telegram', description: 'Push products to telegram channel')]
class PushProductToTelegramChannelCommand extends Command
{
    private const BATCH_SIZE = 20;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SendTelegramService $telegramService,
    ) {
        parent::__construct();
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
            ->setMaxResults(self::BATCH_SIZE)
            ->getQuery()
            ->getResult();

        foreach ($products as $product) {
            $this->telegramService->sendProductToChannel($product);
            $output->writeln('Product ID ' . $product->getId());
        }

        return Command::SUCCESS;
    }
}