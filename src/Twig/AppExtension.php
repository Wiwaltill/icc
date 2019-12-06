<?php

namespace App\Twig;

use App\Converter\EnumStringConverter;
use App\Converter\FilesizeStringConverter;
use App\Converter\StudentStringConverter;
use App\Converter\StudyGroupsGradeStringConverter;
use App\Converter\StudyGroupStringConverter;
use App\Converter\TeacherStringConverter;
use App\Converter\TimestampDateTimeConverter;
use App\Converter\UserStringConverter;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\User;
use MyCLabs\Enum\Enum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension {

    private $teacherConverter;
    private $studentConverter;
    private $userConverter;
    private $studyGroupConverter;
    private $studyGroupsConverter;
    private $filesizeConverter;
    private $timestampConverter;
    private $enumStringConverter;

    public function __construct(TeacherStringConverter $teacherConverter, StudentStringConverter $studentConverter,
                                UserStringConverter $userConverter, StudyGroupStringConverter $studyGroupConverter,
                                StudyGroupsGradeStringConverter $studyGroupsConverter, FilesizeStringConverter $filesizeConverter,
                                TimestampDateTimeConverter $timestampConverter, EnumStringConverter $enumStringConverter) {
        $this->teacherConverter = $teacherConverter;
        $this->studentConverter = $studentConverter;
        $this->userConverter = $userConverter;
        $this->studyGroupConverter = $studyGroupConverter;
        $this->studyGroupsConverter = $studyGroupsConverter;
        $this->filesizeConverter = $filesizeConverter;
        $this->timestampConverter = $timestampConverter;
        $this->enumStringConverter = $enumStringConverter;
    }

    public function getFilters() {
        return [
            new TwigFilter('teacher', [ $this, 'teacher' ]),
            new TwigFilter('student', [ $this, 'student' ]),
            new TwigFilter('user', [ $this, 'user' ]),
            new TwigFilter('studygroup', [$this, 'studyGroup']),
            new TwigFilter('studygroups', [ $this, 'studyGroups' ]),
            new TwigFilter('filesize', [ $this, 'filesize' ]),
            new TwigFilter('todatetime', [ $this, 'toDateTime' ]),
            new TwigFilter('enum', [ $this, 'enum'])
        ];
    }

    public function teacher(?Teacher $teacher, bool $includeAcronym = false) {
        return $this->teacherConverter->convert($teacher, $includeAcronym);
    }

    public function student(Student $student) {
        return $this->studentConverter->convert($student);
    }

    public function user(User $user) {
        return $this->userConverter->convert($user);
    }

    public function studyGroup(StudyGroup $group) {
        return $this->studyGroupConverter->convert($group);
    }

    public function studyGroups(iterable $studyGroups, bool $sort = false) {
        return $this->studyGroupsConverter->convert($studyGroups, $sort);
    }

    public function filesize(int $bytes) {
        return $this->filesizeConverter->convert($bytes);
    }

    public function toDateTime(int $timestamp) {
        return $this->timestampConverter->convert($timestamp);
    }

    public function enum(Enum $enum): string {
        return $this->enumStringConverter->convert($enum);
    }
}