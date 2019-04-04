<?php

namespace App\Request\Data;

use App\Validator\NullOrNotBlank;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubstitutionData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $id;

    /**
     * @Serializer\Type("datetime")
     * @Assert\Date()
     * @var \DateTime
     */
    private $date;

    /**
     * @Serializer\Type("int")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $lessonStart;

    /**
     * @Serializer\Type("int")
     * @Assert\GreaterThan(0)
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart")
     * @var int
     */
    private $lessonEnd;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $type;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $subject;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $replacementSubject;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $teacher;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $replacementTeacher;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $room;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $replacementRoom;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $remark;

    /**
     * @Serializer\Type("array<string>")
     * @var int[]
     */
    private $studyGroups;

    /**
     * @Serializer\Type("array<string>")
     * @var int[]
     */
    private $replacementStudyGroups;

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return SubstitutionData
     */
    public function setId(?string $id): SubstitutionData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return SubstitutionData
     */
    public function setDate(\DateTime $date): SubstitutionData {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    /**
     * @param int $lessonStart
     * @return SubstitutionData
     */
    public function setLessonStart(int $lessonStart): SubstitutionData {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    /**
     * @param int $lessonEnd
     * @return SubstitutionData
     */
    public function setLessonEnd(int $lessonEnd): SubstitutionData {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return SubstitutionData
     */
    public function setType(?string $type): SubstitutionData {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return SubstitutionData
     */
    public function setSubject(?string $subject): SubstitutionData {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReplacementSubject(): ?string {
        return $this->replacementSubject;
    }

    /**
     * @param string|null $replacementSubject
     * @return SubstitutionData
     */
    public function setReplacementSubject(?string $replacementSubject): SubstitutionData {
        $this->replacementSubject = $replacementSubject;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTeacher(): ?string {
        return $this->teacher;
    }

    /**
     * @param string|null $teacher
     * @return SubstitutionData
     */
    public function setTeacher(?string $teacher): SubstitutionData {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReplacementTeacher(): ?string {
        return $this->replacementTeacher;
    }

    /**
     * @param string|null $replacementTeacher
     * @return SubstitutionData
     */
    public function setReplacementTeacher(?string $replacementTeacher): SubstitutionData {
        $this->replacementTeacher = $replacementTeacher;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoom(): ?string {
        return $this->room;
    }

    /**
     * @param string|null $room
     * @return SubstitutionData
     */
    public function setRoom(?string $room): SubstitutionData {
        $this->room = $room;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReplacementRoom(): ?string {
        return $this->replacementRoom;
    }

    /**
     * @param string|null $replacementRoom
     * @return SubstitutionData
     */
    public function setReplacementRoom(?string $replacementRoom): SubstitutionData {
        $this->replacementRoom = $replacementRoom;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRemark(): ?string {
        return $this->remark;
    }

    /**
     * @param string|null $remark
     * @return SubstitutionData
     */
    public function setRemark(?string $remark): SubstitutionData {
        $this->remark = $remark;
        return $this;
    }

    /**
     * @return int[]
     */
    public function getStudyGroups(): array {
        return $this->studyGroups;
    }

    /**
     * @param int[] $studyGroups
     * @return SubstitutionData
     */
    public function setStudyGroups(array $studyGroups): SubstitutionData {
        $this->studyGroups = $studyGroups;
        return $this;
    }

    /**
     * @return int[]
     */
    public function getReplacementStudyGroups(): array {
        return $this->replacementStudyGroups;
    }

    /**
     * @param int[] $replacementStudyGroups
     * @return SubstitutionData
     */
    public function setReplacementStudyGroups(array $replacementStudyGroups): SubstitutionData {
        $this->replacementStudyGroups = $replacementStudyGroups;
        return $this;
    }
}