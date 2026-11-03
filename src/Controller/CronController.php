<?php

namespace JulienIts\EmailsQueueBundle\Controller;

use JulienIts\EmailsQueueBundle\Services\EmailsQueueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class CronController extends AbstractController
{
    public function __construct(
        private readonly EmailsQueueService $emailsQueueService,
    ) {
    }

    #[Route('/emails-queue/cron/process-mail-queue', name: 'emails_queue_cron_process_mail_queue')]
    public function processMailQueue(): Response
    {
        $this->emailsQueueService->processQueue();

        return new Response('Sent');
    }
}
