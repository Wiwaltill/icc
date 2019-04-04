<?php

namespace App\Repository;

use App\Entity\Grade;

interface GradeRepositoryInterface {

    /**
     * @param int $id
     * @return Grade|null
     */
    public function findOneById(int $id): ?Grade;

    /**
     * @param string $name
     * @return Grade|null
     */
    public function findOneByName(string $name): ?Grade;

    /**
     * @return Grade[]
     */
    public function findAll();

    /**
     * @param Grade $grade
     */
    public function persist(Grade $grade): void;

    /**
     * @param Grade $grade
     */
    public function remove(Grade $grade): void;
}