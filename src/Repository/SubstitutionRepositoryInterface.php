<?php

namespace App\Repository;

use App\Entity\Substitution;

interface SubstitutionRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Substitution|null
     */
    public function findOneById(int $id): ?Substitution;

    /**
     * @param string $externalId
     * @return Substitution|null
     */
    public function findOneByExternalId(string $externalId): ?Substitution;

    /**
     * @return Substitution[]
     */
    public function findAll();

    /**
     * @param \DateTime $date
     * @return Substitution[]
     */
    public function findAllByDate(\DateTime $date);

    /**
     * @param Substitution $substitution
     */
    public function persist(Substitution $substitution): void;

    /**
     * @param Substitution $substitution
     */
    public function remove(Substitution $substitution): void;
}