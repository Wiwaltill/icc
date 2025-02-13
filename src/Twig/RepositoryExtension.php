<?php

namespace App\Twig;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\StudyGroup;
use App\Repository\StudyGroupRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RepositoryExtension extends AbstractExtension {

    private StudyGroupRepositoryInterface $studyGroupRepository;

    public function __construct(StudyGroupRepositoryInterface $studyGroupRepository) {
        $this->studyGroupRepository = $studyGroupRepository;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('get_study_group_by_grade', [ $this, 'getStudyGroupByGrade' ])
        ];
    }

    /**
     * @param Grade $grade
     * @param Section $section
     * @return StudyGroup|null
     */
    public function getStudyGroupByGrade(Grade $grade, Section $section): ?StudyGroup {
        return $this->studyGroupRepository->findOneByGrade($grade, $section);
    }
}