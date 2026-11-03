<?php

namespace JulienIts\EmailsQueueBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use JulienIts\EmailsQueueBundle\Entity\EmailQueue;
use JulienIts\EmailsQueueBundle\Entity\EmailSent;
use JulienIts\EmailsQueueBundle\Repository\EmailQueueRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailsQueueService
{
    public const DEFAULT_LIMIT = 15;

    public function __construct(
        protected EntityManagerInterface $em,
        protected MailerInterface $mailer,
        protected EmailQueueRepository $emailQueueRepository,
    ) {
    }

    public function processQueue(int $limit = self::DEFAULT_LIMIT): void
    {
        $emailsQueue = $this->emailQueueRepository->findBy([], ['priority' => 'DESC', 'id' => 'DESC'], $limit);

        foreach ($emailsQueue as $emailQueue) {
            $this->sendEmailQueue($emailQueue);
            $this->setEmailQueueToSent($emailQueue);
        }
    }

    private function sendEmailQueue(EmailQueue $emailQueue): void
    {
        $to = $emailQueue->getEmailTo();

        $message = (new Email())
            ->subject($emailQueue->getSubject())
            ->from(new Address($emailQueue->getEmailFrom(), $emailQueue->getEmailFromName() ?? ''))
            ->to($to)
            ->html($emailQueue->getBody());

        if (!empty($emailQueue->getReplyTo())) {
            $message->addReplyTo($emailQueue->getReplyTo());
        }

        if (null !== $emailQueue->getBodyText()) {
            $message->text($emailQueue->getBodyText());
        }

        foreach ($this->splitEmails($emailQueue->getEmailsCc(), $to) as $email) {
            $message->addCc($email);
        }

        foreach ($this->splitEmails($emailQueue->getEmailsBcc(), $to) as $email) {
            $message->addBcc($email);
        }

        $this->mailer->send($message);
    }

    /**
     * @return list<string>
     */
    private function splitEmails(?string $emails, ?string $exclude): array
    {
        if (null === $emails || '' === trim($emails)) {
            return [];
        }

        $result = [];
        foreach (explode(';', $emails) as $email) {
            $email = trim($email);
            if ('' === $email || $email === $exclude) {
                continue;
            }
            $result[] = $email;
        }

        return $result;
    }

    private function setEmailQueueToSent(EmailQueue $emailQueue): void
    {
        $emailSent = (new EmailSent())
            ->setPriority($emailQueue->getPriority())
            ->setEmailFrom($emailQueue->getEmailFrom())
            ->setEmailFromName($emailQueue->getEmailFromName())
            ->setEmailTo($emailQueue->getEmailTo())
            ->setSubject($emailQueue->getSubject())
            ->setBody($emailQueue->getBody())
            ->setBodyText($emailQueue->getBodyText())
            ->setCreatedOn($emailQueue->getCreatedOn())
            ->setContext($emailQueue->getContext())
            ->setEmailsBcc($emailQueue->getEmailsBcc())
            ->setEmailsCc($emailQueue->getEmailsCc())
            ->setReplyTo($emailQueue->getReplyTo());

        $this->em->persist($emailSent);
        $this->em->remove($emailQueue);
        $this->em->flush();
    }
}
