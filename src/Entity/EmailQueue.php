<?php

namespace JulienIts\EmailsQueueBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JulienIts\EmailsQueueBundle\Repository\EmailQueueRepository;

#[ORM\Entity(repositoryClass: EmailQueueRepository::class)]
#[ORM\Table(name: 'email_queue')]
class EmailQueue
{
    public const HIGH_PRIORITY = 3;
    public const NORMAL_PRIORITY = 2;
    public const LOW_PRIORITY = 1;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'priority', type: Types::SMALLINT, options: ['default' => 1])]
    private ?int $priority = null;

    #[ORM\Column(name: 'emailFrom', type: Types::STRING, length: 50)]
    private ?string $emailFrom = null;

    #[ORM\Column(name: 'emailFromName', type: Types::STRING, length: 50)]
    private ?string $emailFromName = null;

    #[ORM\Column(name: 'emailTo', type: Types::STRING, length: 150)]
    private ?string $emailTo = null;

    #[ORM\Column(name: 'emailsBcc', type: Types::STRING, length: 250, nullable: true)]
    private ?string $emailsBcc = null;

    #[ORM\Column(name: 'emailsCc', type: Types::STRING, length: 250, nullable: true)]
    private ?string $emailsCc = null;

    #[ORM\Column(name: 'replyTo', type: Types::STRING, length: 150, nullable: true)]
    private ?string $replyTo = null;

    #[ORM\Column(name: 'subject', type: Types::STRING, length: 255)]
    private ?string $subject = null;

    #[ORM\Column(name: 'body', type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\Column(name: 'bodyText', type: Types::TEXT, nullable: true)]
    private ?string $bodyText = null;

    #[ORM\ManyToOne(targetEntity: EmailContext::class, inversedBy: 'emailsQueue', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'contextId', referencedColumnName: 'id')]
    private ?EmailContext $context = null;

    #[ORM\Column(name: 'createdOn', type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdOn = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setPriority(?int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setEmailFrom(?string $emailFrom): static
    {
        $this->emailFrom = $emailFrom;

        return $this;
    }

    public function getEmailFrom(): ?string
    {
        return $this->emailFrom;
    }

    public function setEmailFromName(?string $emailFromName): static
    {
        $this->emailFromName = $emailFromName;

        return $this;
    }

    public function getEmailFromName(): ?string
    {
        return $this->emailFromName;
    }

    public function setEmailTo(?string $emailTo): static
    {
        $this->emailTo = $emailTo;

        return $this;
    }

    public function getEmailTo(): ?string
    {
        return $this->emailTo;
    }

    public function setEmailsBcc(?string $emailsBcc): static
    {
        $this->emailsBcc = $emailsBcc;

        return $this;
    }

    public function getEmailsBcc(): ?string
    {
        return $this->emailsBcc;
    }

    public function setEmailsCc(?string $emailsCc): static
    {
        $this->emailsCc = $emailsCc;

        return $this;
    }

    public function getEmailsCc(): ?string
    {
        return $this->emailsCc;
    }

    public function setReplyTo(?string $replyTo): static
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }

    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setBody(?string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBodyText(?string $bodyText): static
    {
        $this->bodyText = $bodyText;

        return $this;
    }

    public function getBodyText(): ?string
    {
        return $this->bodyText;
    }

    public function setContext(?EmailContext $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function getContext(): ?EmailContext
    {
        return $this->context;
    }

    public function setCreatedOn(?\DateTime $createdOn): static
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getCreatedOn(): ?\DateTime
    {
        return $this->createdOn;
    }
}
