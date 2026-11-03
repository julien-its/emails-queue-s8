<?php

namespace JulienIts\EmailsQueueBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JulienIts\EmailsQueueBundle\Repository\EmailContextRepository;

#[ORM\Entity(repositoryClass: EmailContextRepository::class)]
#[ORM\Table(name: 'email_context')]
class EmailContext
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 30)]
    private ?string $name = null;

    /** @var Collection<int, EmailQueue> */
    #[ORM\OneToMany(targetEntity: EmailQueue::class, mappedBy: 'context')]
    private Collection $emailsQueue;

    /** @var Collection<int, EmailSent> */
    #[ORM\OneToMany(targetEntity: EmailSent::class, mappedBy: 'context')]
    private Collection $emailsSent;

    public function __construct()
    {
        $this->emailsQueue = new ArrayCollection();
        $this->emailsSent = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function addEmailsQueue(EmailQueue $emailsQueue): static
    {
        if (!$this->emailsQueue->contains($emailsQueue)) {
            $this->emailsQueue->add($emailsQueue);
        }

        return $this;
    }

    public function removeEmailsQueue(EmailQueue $emailsQueue): static
    {
        $this->emailsQueue->removeElement($emailsQueue);

        return $this;
    }

    /** @return Collection<int, EmailQueue> */
    public function getEmailsQueue(): Collection
    {
        return $this->emailsQueue;
    }

    public function addEmailsSent(EmailSent $emailsSent): static
    {
        if (!$this->emailsSent->contains($emailsSent)) {
            $this->emailsSent->add($emailsSent);
        }

        return $this;
    }

    public function removeEmailsSent(EmailSent $emailsSent): static
    {
        $this->emailsSent->removeElement($emailsSent);

        return $this;
    }

    /** @return Collection<int, EmailSent> */
    public function getEmailsSent(): Collection
    {
        return $this->emailsSent;
    }
}
