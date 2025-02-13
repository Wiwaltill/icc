<?php

namespace App\Request\Data;

use App\Validator\NullOrNotBlank;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TuitionData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $id;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $name;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $displayName;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $subject;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $teachers;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string|null
     */
    private $studyGroup;

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return TuitionData
     */
    public function setId(?string $id): TuitionData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return TuitionData
     */
    public function setName(?string $name): TuitionData {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string {
        return $this->displayName;
    }

    /**
     * @param string|null $displayName
     * @return TuitionData
     */
    public function setDisplayName(?string $displayName): TuitionData {
        $this->displayName = $displayName;
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
     * @return TuitionData
     */
    public function setSubject(?string $subject): TuitionData {
        $this->subject = $subject;
        return $this;
    }


    /**
     * @return string[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @param string[] $teachers
     * @return TuitionData
     */
    public function setTeachers(array $teachers): TuitionData {
        $this->teachers = $teachers;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStudyGroup(): ?string {
        return $this->studyGroup;
    }

    /**
     * @param string|null $studyGroup
     * @return TuitionData
     */
    public function setStudyGroup(?string $studyGroup): TuitionData {
        $this->studyGroup = $studyGroup;
        return $this;
    }
}