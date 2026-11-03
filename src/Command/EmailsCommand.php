<?php

namespace JulienIts\EmailsQueueBundle\Command;

use JulienIts\EmailsQueueBundle\Services\EmailsQueueService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Process the emails queue.
 *
 * Usage: php bin/console jits:queue:process --limit 30
 */
#[AsCommand(
    name: 'jits:queue:process',
    description: 'Process the email queue',
    help: 'This command can be used to process the email queue',
)]
final class EmailsCommand extends Command
{
    public function __construct(
        private readonly EmailsQueueService $emailsQueueService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Maximum number of emails to send', EmailsQueueService::DEFAULT_LIMIT);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = (int) $input->getOption('limit');

        $this->emailsQueueService->processQueue($limit);

        $output->writeln('Queue processed');

        return Command::SUCCESS;
    }
}
