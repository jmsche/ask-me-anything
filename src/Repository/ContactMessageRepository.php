<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ContactMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ContactMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactMessage::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdOn', 'DESC')
            ->getQuery()->getResult();
    }

    public function save(ContactMessage $message): void
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }

    public function delete(ContactMessage $message): void
    {
        $this->getEntityManager()->remove($message);
        $this->getEntityManager()->flush();
    }

    public function markAsRead(ContactMessage $message): void
    {
        $message->setRead(true);
        $this->getEntityManager()->flush();
    }
}
