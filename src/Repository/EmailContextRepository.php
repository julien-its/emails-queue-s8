<?php

namespace JulienIts\EmailsQueueBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JulienIts\EmailsQueueBundle\Entity\EmailContext;

/**
 * @extends ServiceEntityRepository<EmailContext>
 */
class EmailContextRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailContext::class);
    }
}
