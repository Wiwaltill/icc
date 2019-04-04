<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;
use Doctrine\ORM\EntityManagerInterface;

class StudyGroupRepository implements StudyGroupRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @param int $id
     * @return StudyGroup|null
     */
    public function findOneById(int $id): ?StudyGroup {
        return $this->em->getRepository(StudyGroup::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @param string $externalId
     * @return StudyGroup|null
     */
    public function findOneByExternalId(string $externalId): ?StudyGroup {
        return $this->em->getRepository(StudyGroup::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByExternalId(array $externalIds): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('s')
            ->from(StudyGroup::class, 's')
            ->where($qb->expr()->in('s.externalId', ':externalIds'))
            ->setParameter('externalIds', $externalIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Grade $grade
     * @param StudyGroupType|null $type
     * @return StudyGroup[]
     */
    public function findAllByGrades(Grade $grade, ?StudyGroupType $type = null) {
        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('sgInner.id')
            ->from(StudyGroup::class, 'sgInner')
            ->leftJoin('sgInner.grades', 'gInner')
            ->where('gInner.id = :grade');

        if($type !== null) {
            $qbInner
                ->andWhere('sgInner.type = :type');

            $qb->setParameter('type', $type);
        }

        $qb
            ->select(['sg', 'g'])
            ->from(StudyGroup::class, 'sg')
            ->leftJoin('sg.grades', 'g')
            ->where(
                $qb->expr()->in('sg.id', $qbInner->getDQL())
            )
            ->setParameter('grade', $grade->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Student $student
     * @return StudyGroup[]
     */
    public function findAllByStudent(Student $student) {
        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('sgInner.id')
            ->from(StudyGroup::class, 'sgInner')
            ->leftJoin('sgInner.students', 's')
            ->where('sgInner.id = :student');

        $qb
            ->select(['sg', 'g'])
            ->from(StudyGroup::class, 'sg')
            ->leftJoin('sg.grades', 'g')
            ->where(
                $qb->expr()->in('sg.id', $qbInner->getDQL())
            )
            ->setParameter('student', $student->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @return StudyGroup[]
     */
    public function findAll() {
        return $this->em->getRepository(StudyGroup::class)
            ->findAll();
    }

    /**
     * @param StudyGroup $studyGroup
     */
    public function persist(StudyGroup $studyGroup): void {
        $this->em->persist($studyGroup);
        $this->em->flush();
    }

    /**
     * @param StudyGroup $studyGroup
     */
    public function remove(StudyGroup $studyGroup): void {
        $this->em->remove($studyGroup);
        $this->em->flush();
    }

}