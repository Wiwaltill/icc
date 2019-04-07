<?php

namespace App\Repository;

use App\Entity\TimetablePeriod;
use App\Entity\TimetableSupervision;
use Doctrine\ORM\EntityManagerInterface;

class TimetableSupervisionRepository implements TimetableSupervisionRepositoryInterface {

    private $em;
    private $isTransactionActive = false;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?TimetableSupervision {
        return $this->em->getRepository(TimetableSupervision::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByPeriod(TimetablePeriod $period) {
        return $this->em->getRepository(TimetableSupervision::class)
            ->findOneBy([
                'period' => $period
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(TimetableSupervision::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(TimetableSupervision $supervision): void {
        $this->em->persist($supervision);
        $this->isTransactionActive || $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(TimetableSupervision $supervision): void {
        $this->em->remove($supervision);
        $this->isTransactionActive || $this->em->flush();
    }

    public function beginTransaction(): void {
        $this->em->beginTransaction();
        $this->isTransactionActive = true;
    }

    public function commit(): void {
        $this->em->flush();
        $this->em->commit();
        $this->isTransactionActive = false;
    }
}