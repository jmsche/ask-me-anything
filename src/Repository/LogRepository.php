<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Log;
use App\Entity\Step;
use App\Entity\Tutorial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LogRepository extends ServiceEntityRepository
{
    private TranslatorInterface $translator;

    public function __construct(ManagerRegistry $registry, TranslatorInterface $translator)
    {
        parent::__construct($registry, Log::class);
        $this->translator = $translator;
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.id', 'DESC')
            ->getQuery()->getResult();
    }

    public function create(Request $request, Tutorial $tutorial, ?Step $step): void
    {
        if (null !== $step) {
            $label = $this->translator->trans('log.create.tutorial_and_step', [
                '%tutorial%' => $tutorial->getName(),
                '%step%'     => $step->getNumber(),
            ]);
        } else {
            $label = $this->translator->trans('log.create.tutorial', [
                '%tutorial%' => $tutorial->getName(),
            ]);
        }

        $log = new Log(
            $request->getClientIp(),
            $request->headers->get('User-Agent'),
            $request->getUri(),
            $label,
        );

        $this->getEntityManager()->persist($log);
        $this->getEntityManager()->flush();

        if (500 < $this->totalLogs()) {
            $this->getEntityManager()->getConnection()->executeQuery('DELETE FROM ' . $this->getClassMetadata()->getTableName() . ' ORDER BY id ASC LIMIT 1;');
        }
    }

    public function deleteAllData(): void
    {
        $this->getEntityManager()->getConnection()->executeQuery('DELETE FROM ' . $this->getClassMetadata()->getTableName());
    }

    private function totalLogs(): int
    {
        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->getQuery()->getSingleScalarResult();
    }
}
