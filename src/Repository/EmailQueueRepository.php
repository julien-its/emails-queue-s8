<?php

namespace JulienIts\EmailsQueueBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JulienIts\EmailsQueueBundle\Entity\EmailQueue;

/**
 * @extends ServiceEntityRepository<EmailQueue>
 */
class EmailQueueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailQueue::class);
    }
}
