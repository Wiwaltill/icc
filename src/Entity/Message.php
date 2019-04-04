<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Message {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $subject;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", name="start_date")
     * @Assert\Date()
     * @Assert\NotNull()
     * @var \DateTime
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", name="expire_date")
     * @Assert\GreaterThan(propertyPath="startDate")
     * @Assert\Date()
     * @Assert\NotNull()
     * @var \DateTime
     */
    private $expireDate;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup")
     * @ORM\JoinTable(
     *     name="message_studygroups",
     *     joinColumns={@ORM\JoinColumn(name="message", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="studygroup", onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     * @var ArrayCollection<StudyGroup>
     */
    private $studyGroups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MessageAttachment", mappedBy="message")
     * @var ArrayCollection<MessageAttachment>
     */
    private $attachments;

    /**
     * @ORM\OneToMany(targetEntity="MessageVisibility", mappedBy="message", cascade={"persist"})
     * @var ArrayCollection<MessageVisibility>
     */
    private $visibilities;

    /**
     * @ORM\Column(type="MessageScope::class")
     * @var MessageScope
     */
    private $scope;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Gedmo\Blameable(on="create")
     * @var User
     */
    private $createdBy = null;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isDownloadsEnabled = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isUploadsEnabled = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string|null
     */
    private $uploadDescription;

    /**
     * @ORM\OneToMany(targetEntity="MessageFile", mappedBy="message")
     * @var ArrayCollection<MessageFile>
     */
    private $files;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $hiddenFromDashboard = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isNotificationSent = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $mustConfirm = false;

    /**
     * @ORM\OneToMany(targetEntity="MessageConfirmation", mappedBy="message")
     * @var ArrayCollection<MessageConfirmation>
     */
    private $confirmations;

    public function __construct() {
        $this->studyGroups = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->visibilities = new ArrayCollection();
        $this->confirmations = new ArrayCollection();

        $this->scope = MessageScope::None();
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
    public function getSubject(): string {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return Message
     */
    public function setSubject(string $subject): Message {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Message
     */
    public function setContent(string $content): Message {
        $this->content = $content;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return Message
     */
    public function setStartDate(\DateTime $startDate): Message {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpireDate(): \DateTime {
        return $this->expireDate;
    }

    /**
     * @param \DateTime $expireDate
     * @return Message
     */
    public function setExpireDate(\DateTime $expireDate): Message {
        $this->expireDate = $expireDate;
        return $this;
    }

    public function addStudyGroup(StudyGroup $studyGroup) {
        $this->studyGroups->add($studyGroup);
    }

    public function removeStudyGroups(StudyGroup $studyGroup) {
        $this->studyGroups->removeElement($studyGroup);
    }

    /**
     * @return ArrayCollection<StudyGroup>
     */
    public function getStudyGroups(): ArrayCollection {
        return $this->studyGroups;
    }

    public function addAttachment(MessageAttachment $attachment) {
        $this->attachments->add($attachment);
    }

    public function removeAttachment(MessageAttachment $attachment) {
        $this->attachments->removeElement($attachment);
    }

    /**
     * @return ArrayCollection<MessageAttachment>
     */
    public function getAttachments(): ArrayCollection {
        return $this->attachments;
    }

    public function addVisibility(MessageVisibility $visibility) {
        $this->visibilities->add($visibility);
    }

    public function removeVisibility(MessageVisibility $visibility) {
        $this->visibilities->removeElement($visibility);
    }

    /**
     * @return ArrayCollection<MessageVisibility>
     */
    public function getVisibilities(): ArrayCollection {
        return $this->visibilities;
    }

    /**
     * @return MessageScope
     */
    public function getScope(): MessageScope {
        return $this->scope;
    }

    /**
     * @param MessageScope $scope
     * @return Message
     */
    public function setScope(MessageScope $scope): Message {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User {
        return $this->createdBy;
    }

    /**
     * @return bool
     */
    public function isDownloadsEnabled(): bool {
        return $this->isDownloadsEnabled;
    }

    /**
     * @param bool $isDownloadsEnabled
     * @return Message
     */
    public function setIsDownloadsEnabled(bool $isDownloadsEnabled): Message {
        $this->isDownloadsEnabled = $isDownloadsEnabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUploadsEnabled(): bool {
        return $this->isUploadsEnabled;
    }

    /**
     * @param bool $isUploadsEnabled
     * @return Message
     */
    public function setIsUploadsEnabled(bool $isUploadsEnabled): Message {
        $this->isUploadsEnabled = $isUploadsEnabled;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUploadDescription(): ?string {
        return $this->uploadDescription;
    }

    /**
     * @param string|null $uploadDescription
     * @return Message
     */
    public function setUploadDescription(?string $uploadDescription): Message {
        $this->uploadDescription = $uploadDescription;
        return $this;
    }

    public function addFile(MessageFile $file) {
        $this->files->add($file);
    }

    public function removeFile(MessageFile $file) {
        $this->files->removeElement($file);
    }

    /**
     * @return ArrayCollection<MessageFile>
     */
    public function getFiles(): ArrayCollection {
        return $this->files;
    }

    /**
     * @return bool
     */
    public function isHiddenFromDashboard(): bool {
        return $this->hiddenFromDashboard;
    }

    /**
     * @param bool $hiddenFromDashboard
     * @return Message
     */
    public function setHiddenFromDashboard(bool $hiddenFromDashboard): Message {
        $this->hiddenFromDashboard = $hiddenFromDashboard;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNotificationSent(): bool {
        return $this->isNotificationSent;
    }

    /**
     * @param bool $isNotificationSent
     * @return Message
     */
    public function setIsNotificationSent(bool $isNotificationSent): Message {
        $this->isNotificationSent = $isNotificationSent;
        return $this;
    }

    /**
     * @return bool
     */
    public function mustConfirm(): bool {
        return $this->mustConfirm;
    }

    /**
     * @param bool $mustConfirm
     * @return Message
     */
    public function setMustConfirm(bool $mustConfirm): Message {
        $this->mustConfirm = $mustConfirm;
        return $this;
    }

    /**
     * @return Collection<MessageConfirmation>
     */
    public function getConfirmations(): Collection {
        return $this->confirmations;
    }
}