<?php

namespace App\Entity;

use App\Validator\NullOrNotBlank;
use DateTime;
use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @UniqueEntity(fields={"externalId"})
 */
class Student {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotNull()
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $lastname;

    /**
     * @ORM\Column(type="gender")
     * @var Gender
     */
    private $gender;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @NullOrNotBlank()
     * @Assert\Email()
     * @var string|null
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $status;

    /**
     * @ORM\Column(type="date")
     * @var DateTime
     */
    private $birthday;

    /**
     * @ORM\ManyToOne(targetEntity="Grade", inversedBy="students")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\NotNull()
     * @var Grade|null
     */
    private $grade;

    /**
     * @ORM\OneToMany(targetEntity="StudyGroupMembership", mappedBy="student")
     * @var Collection<StudyGroupMembership>
     */
    private $studyGroupMemberships;

    /**
     * @ORM\ManyToMany(targetEntity="PrivacyCategory")
     * @ORM\JoinTable(name="student_approved_privacy_categories",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<PrivacyCategory>
     */
    private $approvedPrivacyCategories;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->gender = Gender::X();
        $this->studyGroupMemberships = new ArrayCollection();
        $this->approvedPrivacyCategories = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return Student
     */
    public function setExternalId(?string $externalId): Student {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return Student
     */
    public function setFirstname(?string $firstname): Student {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return Student
     */
    public function setLastname(?string $lastname): Student {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return Gender|null
     */
    public function getGender(): ?Gender {
        return $this->gender;
    }

    /**
     * @param Gender|null $gender
     * @return Student
     */
    public function setGender(?Gender $gender): Student {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return Student
     */
    public function setEmail(?string $email): Student {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return Student
     */
    public function setStatus(?string $status): Student {
        $this->status = $status;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBirthday(): DateTime {
        return $this->birthday;
    }

    /**
     * @param DateTime $birthday
     * @return Student
     */
    public function setBirthday(DateTime $birthday): Student {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFullAged(DateTime $today): bool {
        $diff = date_diff($this->getBirthday(), $today);
        $age = $diff->y;
        return $age >= 18;
    }

    /**
     * @return Grade|null
     */
    public function getGrade(): ?Grade {
        return $this->grade;
    }

    /**
     * @param Grade|null $grade
     * @return Student
     */
    public function setGrade(?Grade $grade): Student {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return Collection<StudyGroupMembership>
     */
    public function getStudyGroupMemberships(): Collection {
        return $this->studyGroupMemberships;
    }

    /**
     * @return Collection<PrivacyCategory>
     */
    public function getApprovedPrivacyCategories(): Collection {
        return $this->approvedPrivacyCategories;
    }

    public function __toString() {
        return sprintf('%s: %s, %s', $this->getGrade(), $this->getLastname(), $this->getFirstname());
    }
}