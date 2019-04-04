<?php

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;

class SubjectRepository implements SubjectRepositoryInterface {
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @param int $id
     * @return Subject|null
     */
    public function findOneById(int $id): ?Subject {
        return $this->em->getRepository(Subject::class)
            ->findOneBy([
                'id'=> $id
            ]);
    }

    /**
     * @param string $abbreviation
     * @return Subject|null
     */
    public function findOneByAbbreviation(string $abbreviation): ?Subject {
        return $this->em->getRepository(Subject::class)
            ->findOneBy([
                'abbreviation' => $abbreviation
            ]);
    }

    /**
     * @return Subject[]
     */
    public function findAll() {
        return $this->em->getRepository(Subject::class)
            ->findAll();
    }

    /**
     * @param Subject $subject
     */
    public function persist(Subject $subject): void {
        $this->em->persist($subject);
        $this->em->flush();
    }

    /**
     * @param Subject $subject
     */
    public function remove(Subject $subject): void {
        $this->em->remove($subject);
        $this->em->flush();
    }
}