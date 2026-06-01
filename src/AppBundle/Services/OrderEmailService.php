<?php

namespace AppBundle\Services;

use OrderBundle\Entity\Order;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class OrderEmailService
{
    private Environment $twig;
    private LoggerInterface $logger;
    private string $mailerTransport;
    private string $mailerHost;
    private ?string $mailerUser;
    private ?string $mailerPassword;
    private string $orderEmail;
    private string $companyName;
    private string $fullDomain;

    public function __construct(
        Environment $twig,
        LoggerInterface $logger,
        string $mailerTransport,
        string $mailerHost,
        ?string $mailerUser,
        ?string $mailerPassword,
        string $orderEmail,
        string $companyName,
        string $fullDomain
    ) {
        $this->twig = $twig;
        $this->logger = $logger;
        $this->mailerTransport = $mailerTransport;
        $this->mailerHost = $mailerHost;
        $this->mailerUser = $mailerUser;
        $this->mailerPassword = $mailerPassword;
        $this->orderEmail = $orderEmail;
        $this->companyName = $companyName;
        $this->fullDomain = rtrim($fullDomain, '/');
    }

    public function sendOrderCreatedEmail(Order $order): bool
    {
        $recipient = trim((string) $order->getEmail());
        if ('' === $recipient || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!$this->isConfigured()) {
            $this->logger->warning('Order email skipped: mailer is not configured', [
                'order_id' => $order->getId(),
            ]);

            return false;
        }

        try {
            $mailer = new Mailer(Transport::fromDsn($this->buildDsn()));
            $email = (new Email())
                ->from(new Address($this->orderEmail, $this->companyName))
                ->to($recipient)
                ->replyTo(new Address($this->orderEmail, $this->companyName))
                ->subject(sprintf('%s #%s', $this->companyName, $order->getId()))
                ->html($this->twig->render('@App/Email/order_confirmation.html.twig', [
                    'order' => $order,
                    'company_name' => $this->companyName,
                    'site_url' => $this->fullDomain,
                ]));

            $mailer->send($email);

            return true;
        } catch (\Throwable $e) {
            $this->logger->error('Order email send failed', [
                'order_id' => $order->getId(),
                'exception' => $e,
            ]);

            return false;
        }
    }

    private function buildDsn(): string
    {
        $auth = '';

        if ($this->mailerUser) {
            $auth = rawurlencode($this->mailerUser);
            if ($this->mailerPassword) {
                $auth .= ':' . rawurlencode($this->mailerPassword);
            }
            $auth .= '@';
        }

        return sprintf('%s://%s%s', $this->mailerTransport, $auth, $this->mailerHost);
    }

    private function isConfigured(): bool
    {
        return '' !== trim($this->mailerTransport)
            && '' !== trim($this->mailerHost)
            && filter_var($this->orderEmail, FILTER_VALIDATE_EMAIL);
    }
}
