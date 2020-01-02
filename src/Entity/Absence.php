<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Absence {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private $lessonStart;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private $lessonEnd;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher")
     * @ORM\JoinColumn()
     * @var Teacher
     */
    private $teacher;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StudyGroup")
     * @ORM\JoinColumn()
     * @var StudyGroup
     */
    private $studyGroup;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Absence
     */
    public function setDate(\DateTime $date): Absence {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLessonStart(): ?int {
        return $this->lessonStart;
    }

    /**
     * @param int|null $lessonStart
     * @return Absence
     */
    public function setLessonStart(?int $lessonStart): Absence {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLessonEnd(): ?int {
        return $this->lessonEnd;
    }

    /**
     * @param int|null $lessonEnd
     * @return Absence
     */
    public function setLessonEnd(?int $lessonEnd): Absence {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher $teacher
     * @return Absence
     */
    public function setTeacher(Teacher $teacher): Absence {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return StudyGroup|null
     */
    public function getStudyGroup(): ?StudyGroup {
        return $this->studyGroup;
    }

    /**
     * @param StudyGroup $studyGroup
     * @return Absence
     */
    public function setStudyGroup(StudyGroup $studyGroup): Absence {
        $this->studyGroup = $studyGroup;
        return $this;
    }

}