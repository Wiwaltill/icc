<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Sorting\StudentGroupMembershipStrategy;
use DateTime;
use Doctrine\ORM\QueryBuilder;

interface StudentRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Student|null
     */
    public function findOneById(int $id): ?Student;

    /**
     * @param string $uuid
     * @return Student|null
     */
    public function findOneByUuid(string $uuid): ?Student;

    /**
     * @param string $externalId
     * @return Student|null
     */
    public function findOneByExternalId(string $externalId): ?Student;

    /**
     * @param string[] $externalIds
     * @return Student[]
     */
    public function findAllByExternalId(array $externalIds): array;

    /**
     * @param Grade $grade
     * @param Section $section
     * @return Student[]
     */
    public function findAllByGrade(Grade $grade, Section $section): array;

    /**
     * @param string $query
     * @return Student[]
     */
    public function findAllByQuery(string $query): array;

    /**
     * @param StudyGroup[] $studyGroups
     * @return Student[]
     */
    public function findAllByStudyGroups(array $studyGroups): array;

    /**
     * @param StudyGroup[] $studyGroups
     * @return QueryBuilder
     */
    public function getQueryBuilderFindAllByStudyGroups(array $studyGroups): QueryBuilder;

    /**
     * @return Student[]
     */
    public function findAll();

    /**
     * @param Section $section
     * @return Student[]
     */
    public function findAllBySection(Section $section): array;

    /**
     * @param Student $student
     */
    public function persist(Student $student): void;

    /**
     * @param Student $student
     */
    public function remove(Student $student): void;

    /**
     * Removes all students without any grade membership.
     * @return int Number of removed students
     */
    public function removeOrphaned(): int;
}