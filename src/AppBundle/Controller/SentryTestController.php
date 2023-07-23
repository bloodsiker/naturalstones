<?php

namespace AppBundle\Controller;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SentryTestController extends AbstractController {
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function testLogAction()
    {
        // the following code will test if monolog integration logs to sentry
        $this->logger->error('My custom logged error.');

        // the following code will test if an uncaught exception logs to sentry
        throw new RuntimeException('Example exception.');
    }
}