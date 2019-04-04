<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"externalId"})
 */
class Tuition {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $externalId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Subject
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Teacher
     */
    private $teacher;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher")
     * @ORM\JoinTable(
     *     name="tuition_teachers",
     *     joinColumns={@ORM\JoinColumn(name="studygroup", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="teacher", referencedColumnName="id")}
     * )
     * @var ArrayCollection<Teacher>
     */
    private $additionalTeachers;

    /**
     * @ORM\ManyToOne(targetEntity="StudyGroup", inversedBy="tuitions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var StudyGroup
     */
    private $studyGroup;

    public function __construct() {
        $this->additionalTeachers = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getExternalId(): string {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return Tuition
     */
    public function setExternalId(string $externalId): Tuition {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Tuition
     */
    public function setName(string $name): Tuition {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Subject
     */
    public function getSubject(): Subject {
        return $this->subject;
    }

    /**
     * @param Subject $subject
     * @return Tuition
     */
    public function setSubject(Subject $subject): Tuition {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return Teacher
     */
    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher $teacher
     * @return Tuition
     */
    public function setTeacher(Teacher $teacher): Tuition {
        $this->teacher = $teacher;
        return $this;
    }

    public function addAdditionalTeacher(Teacher $teacher) {
        $this->additionalTeachers->add($teacher);
    }

    public function removeAdditionalTeacher(Teacher $teacher) {
        $this->additionalTeachers->removeElement($teacher);
    }

    /**
     * @return ArrayCollection<Teacher>
     */
    public function getAdditionalTeachers(): ArrayCollection {
        return $this->additionalTeachers;
    }

    /**
     * @return StudyGroup
     */
    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }

    /**
     * @param StudyGroup $studyGroup
     * @return Tuition
     */
    public function setStudyGroup(StudyGroup $studyGroup): Tuition {
        $this->studyGroup = $studyGroup;
        return $this;
    }
}