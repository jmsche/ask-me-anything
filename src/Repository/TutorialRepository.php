<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Tutorial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class TutorialRepository extends ServiceEntityRepository
{
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(ManagerRegistry $registry, AuthorizationCheckerInterface $authorizationChecker)
    {
        parent::__construct($registry, Tutorial::class);
        $this->authorizationChecker = $authorizationChecker;
    }

    public function findByCategory(Category $category): array
    {
        $qb = $this->createQueryBuilder('t')
            ->addSelect('c')
            ->innerJoin('t.category', 'c')
            ->where('c.id = :category')
            ->setParameter('category', $category->getId())
            ->orderBy('t.name', 'ASC');

        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $qb->andWhere('t.visible = 1');
        }

        return $qb->getQuery()->getResult();
    }

    public function save(Tutorial $tutorial): void
    {
        $this->getEntityManager()->persist($tutorial);
        $this->getEntityManager()->flush();
    }

    public function delete(Tutorial $tutorial): void
    {
        $this->getEntityManager()->remove($tutorial);
        $this->getEntityManager()->flush();
    }
}
