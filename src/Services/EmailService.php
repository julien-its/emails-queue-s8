<?php

namespace JulienIts\EmailsQueueBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use JulienIts\EmailsQueueBundle\Entity\EmailContext;
use JulienIts\EmailsQueueBundle\Entity\EmailQueue;
use JulienIts\EmailsQueueBundle\Repository\EmailContextRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;

class EmailService
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected Environment $twig,
        protected EmailsQueueService $emailsQueueService,
        protected EmailContextRepository $emailContextRepository,
        #[Autowire(param: 'emails_queue.mode')]
        protected ?string $mode,
        #[Autowire(param: 'emails_queue.debug_to')]
        protected ?string $debugTo,
        #[Autowire(param: 'emails_queue.debug_cc')]
        protected ?string $debugCc,
    ) {
    }

    /**
     * @param array<string, mixed> $config
     */
    public function createNewAndProcess(array $config): void
    {
        $this->createNew($config);
        $this->emailsQueueService->processQueue(1);
    }

    /**
     * Supported keys:
     *  - template + templateVars, or emailHtml
     *  - templateText (optional)
     *  - contextName, emailFrom, emailFromName, emailTo, subject, priority
     *  - replyTo, emailsCc, emailsBcc (optional, multiple emails separated by ";")
     *
     * @param array<string, mixed> $config
     */
    public function createNew(array $config): EmailQueue
    {
        $templateVars = $config['templateVars'] ?? [];

        $emailHtml = \array_key_exists('emailHtml', $config)
            ? $config['emailHtml']
            : $this->twig->render($config['template'], $templateVars);

        $context = $this->emailContextRepository->findOneBy(['name' => $config['contextName']]);
        if (null === $context) {
            $context = (new EmailContext())->setName($config['contextName']);
            $this->em->persist($context);
        }

        $emailQueue = (new EmailQueue())
            ->setBody($emailHtml)
            ->setContext($context)
            ->setEmailFrom($config['emailFrom'])
            ->setEmailFromName($config['emailFromName'])
            ->setPriority($config['priority'] ?? EmailQueue::NORMAL_PRIORITY)
            ->setSubject($config['subject'])
            ->setCreatedOn(new \DateTime());

        if (isset($config['replyTo'])) {
            $emailQueue->setReplyTo($config['replyTo']);
        }

        if ('prod' === $this->mode) {
            $emailQueue->setEmailTo($config['emailTo']);
            if (isset($config['emailsCc'])) {
                $emailQueue->setEmailsCc($config['emailsCc']);
            }
            if (isset($config['emailsBcc'])) {
                $emailQueue->setEmailsBcc($config['emailsBcc']);
            }
        } else {
            $emailQueue->setEmailTo(!empty($this->debugTo) ? $this->debugTo : 'info@'.gethostname());
            if (!empty($this->debugCc)) {
                $emailQueue->setEmailsCc($this->debugCc);
            }
            $emailQueue->setBody(
                $emailQueue->getBody().PHP_EOL.PHP_EOL.PHP_EOL
                .'[DEBUG] Ce message a été envoyé a '.$config['emailTo']
            );
        }

        if (isset($config['templateText'])) {
            $emailQueue->setBodyText($this->twig->render($config['templateText'], $templateVars));
        }

        $this->em->persist($emailQueue);
        $this->em->flush();

        return $emailQueue;
    }
}
