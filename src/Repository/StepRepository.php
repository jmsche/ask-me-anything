<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Step;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class StepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Step::class);
    }

    public function invertSteps(Step $step1, Step $step2): void
    {
        $weight1 = $step1->getWeight();
        $step1->setWeight($step2->getWeight());
        $step2->setWeight($weight1);
        $this->getEntityManager()->flush();
    }

    public function save(Step $step): void
    {
        $this->getEntityManager()->persist($step);
        $this->getEntityManager()->flush();
    }

    public function delete(Step $step): void
    {
        $this->getEntityManager()->remove($step);
        $this->getEntityManager()->flush();
    }
}
