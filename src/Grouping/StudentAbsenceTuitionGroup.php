<?php

namespace App\Grouping;

use App\Entity\StudentAbsence;
use App\Entity\Tuition;

class StudentAbsenceTuitionGroup implements GroupInterface, SortableGroupInterface {

    /** @var Tuition */
    private Tuition $tuition;

    /** @var StudentAbsence[] */
    private array $absences = [ ];

    public function __construct(Tuition $tuition) {
        $this->tuition = $tuition;
    }

    /**
     * @return Tuition
     */
    public function getTuition(): Tuition {
        return $this->tuition;
    }

    /**
     * @return StudentAbsence[]
     */
    public function getAbsences(): array {
        return $this->absences;
    }

    public function getKey() {
        return $this->tuition;
    }

    public function addItem($item) {
        $this->absences[] = $item;
    }

    public function &getItems(): array {
        return $this->absences;
    }
}