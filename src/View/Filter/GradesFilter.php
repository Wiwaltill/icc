<?php

namespace App\View\Filter;

use App\Entity\User;
use App\Sorting\GradeNameStrategy;

class GradesFilter extends AbstractGradeFilter {
    
    public function handle(array $gradeUuids, User $user) {
        $grades = $this->getGrades($user, $defaultGrade);
        $selectedGrades = [ ];

        foreach($grades as $grade) {
            if(in_array((string)$grade->getUuid(), $gradeUuids)) {
                $selectedGrades[] = $grade;
            }
        }

        $this->sorter->sort($grades, GradeNameStrategy::class);

        return new GradesFilterView($grades, $selectedGrades);
    }
}