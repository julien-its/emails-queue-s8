<?php

namespace JulienIts\EmailsQueueBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JulienIts\EmailsQueueBundle\Entity\EmailSent;

/**
 * @extends ServiceEntityRepository<EmailSent>
 */
class EmailSentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailSent::class);
    }
}
