<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;

interface UserRepositoryInterface extends TransactionalRepositoryInterface {
    /**
     * @param int $id
     * @return User|null
     */
    public function findOneById(int $id): ?User;

    /**
     * @param string $uuid
     * @return User|null
     */
    public function findOneByUuid(string $uuid): ?User;

    /**
     * @param string $username
     * @return User|null
     */
    public function findOneByUsername(string $username): ?User;

    /**
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User;

    /**
     * @param Student $student
     * @return User[]
     */
    public function findAllByStudent(Student $student);

    /**
     * @param Student[] $students
     * @return User[]
     */
    public function findAllParentsByStudents(array $students): array;

    /**
     * @param Student[] $students
     * @return User[]
     */
    public function findAllStudentsByStudents(array $students): array;

    /**
     * @param Teacher[] $teachers
     * @return User[]
     */
    public function findAllTeachers(array $teachers): array;


    /**
     * @return User[]
     */
    public function findAllByNotifyExams();

    /**
     * @return User[]
     */
    public function findAllByNotifySubstitutions();

    /**
     * @param Message $message Restrict users to only those who are in the message recipients.
     * @return User[]
     */
    public function findAllByNotifyMessages(Message $message);

    /**
     * @param UserType[] $types
     * @return User[]
     */
    public function findAllByUserTypes(array $types): array;

    /**
     * @return User[]
     */
    public function findAll();

    /**
     * @param User $user
     */
    public function persist(User $user): void;

    /**
     * @param User $user
     */
    public function remove(User $user): void;

    /**
     * Removes orphaned users.
     *
     * @return int Number of users removed from the system.
     */
    public function removeOrphaned(): int;
}