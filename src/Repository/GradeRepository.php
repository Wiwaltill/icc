<?php

namespace App\Repository;

use App\Entity\Grade;
use Doctrine\ORM\EntityManagerInterface;

class GradeRepository implements GradeRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @param int $id
     * @return Grade|null
     */
    public function findOneById(int $id): ?Grade {
        return $this->em->getRepository(Grade::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByName(string $name): ?Grade {
        return $this->em->getRepository(Grade::class)
            ->findOneBy([
                'name' => $name
            ]);
    }

    /**
     * @return Grade[]
     */
    public function findAll() {
        return $this->em->getRepository(Grade::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    /**
     * @param Grade $grade
     */
    public function persist(Grade $grade): void {
        $this->em->persist($grade);
        $this->em->flush();
    }

    /**
     * @param Grade $grade
     */
    public function remove(Grade $grade): void {
        $this->em->remove($grade);
        $this->em->flush();
    }

}