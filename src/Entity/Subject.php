<?php

namespace App\Entity;

use App\Validator\Color;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @UniqueEntity(fields={"abbreviation"})
 */
class Subject {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @var string|null
     */
    private $abbreviation;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotNull()
     * @var string|null
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $replaceSubjectAbbreviation = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleGrades = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleStudents = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleTeachers = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleRooms = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleSubjects = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleLists = true;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(min="7", max="7")
     * @Color()
     * @Serializer\Exclude()
     * @var string|null
     */
    private $color;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher", mappedBy="subjects")
     * @var ArrayCollection<Teacher>
     */
    private $teachers;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->teachers = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return Subject
     */
    public function setExternalId(?string $externalId): Subject {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAbbreviation(): ?string {
        return $this->abbreviation;
    }

    /**
     * @param string|null $abbreviation
     * @return Subject
     */
    public function setAbbreviation(?string $abbreviation): Subject {
        $this->abbreviation = $abbreviation;
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
     * @return Subject
     */
    public function setName(?string $name): Subject {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isReplaceSubjectAbbreviation(): bool {
        return $this->replaceSubjectAbbreviation;
    }

    /**
     * @param bool $replaceSubjectAbbreviation
     * @return Subject
     */
    public function setReplaceSubjectAbbreviation(bool $replaceSubjectAbbreviation): Subject {
        $this->replaceSubjectAbbreviation = $replaceSubjectAbbreviation;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleGrades(): bool {
        return $this->isVisibleGrades;
    }

    /**
     * @param bool $isVisibleGrades
     * @return Subject
     */
    public function setIsVisibleGrades(bool $isVisibleGrades): Subject {
        $this->isVisibleGrades = $isVisibleGrades;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleStudents(): bool {
        return $this->isVisibleStudents;
    }

    /**
     * @param bool $isVisibleStudents
     * @return Subject
     */
    public function setIsVisibleStudents(bool $isVisibleStudents): Subject {
        $this->isVisibleStudents = $isVisibleStudents;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleTeachers(): bool {
        return $this->isVisibleTeachers;
    }

    /**
     * @param bool $isVisibleTeachers
     * @return Subject
     */
    public function setIsVisibleTeachers(bool $isVisibleTeachers): Subject {
        $this->isVisibleTeachers = $isVisibleTeachers;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleRooms(): bool {
        return $this->isVisibleRooms;
    }

    /**
     * @param bool $isVisibleRooms
     * @return Subject
     */
    public function setIsVisibleRooms(bool $isVisibleRooms): Subject {
        $this->isVisibleRooms = $isVisibleRooms;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleSubjects(): bool {
        return $this->isVisibleSubjects;
    }

    /**
     * @param bool $isVisibleSubjects
     * @return Subject
     */
    public function setIsVisibleSubjects(bool $isVisibleSubjects): Subject {
        $this->isVisibleSubjects = $isVisibleSubjects;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleLists(): bool {
        return $this->isVisibleLists;
    }

    /**
     * @param bool $isVisibleLists
     * @return Subject
     */
    public function setIsVisibleLists(bool $isVisibleLists): Subject {
        $this->isVisibleLists = $isVisibleLists;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string {
        return $this->color;
    }

    /**
     * @param string|null $color
     * @return Subject
     */
    public function setColor(?string $color): Subject {
        $this->color = $color;
        return $this;
    }

    /**
     * @return ArrayCollection<Teacher>
     */
    public function getTeachers(): ArrayCollection {
        return $this->teachers;
    }

    public function __toString() {
        return sprintf('%s [%s]', $this->getName(), $this->getAbbreviation());
    }

}