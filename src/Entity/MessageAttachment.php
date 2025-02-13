<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @Auditable()
 * @Vich\Uploadable()
 */
class MessageAttachment {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="attachments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Message
     */
    private $message;

    /**
     * @Vich\UploadableField(mapping="messages", fileNameProperty="path", originalName="filename", size="size")
     */
    private $file;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $filename;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $path;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $size;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $updatedAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return Message|null
     */
    public function getMessage(): ?Message {
        return $this->message;
    }

    /**
     * @param Message $message
     * @return MessageAttachment
     */
    public function setMessage(Message $message): MessageAttachment {
        $this->message = $message;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File {
        return $this->file;
    }

    /**
     * @param File|null $file
     * @return MessageAttachment
     */
    public function setFile(?File $file = null): MessageAttachment {
        $this->file = $file;

        if($file !== null) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return MessageAttachment
     */
    public function setFilename(?string $filename): MessageAttachment {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): ?string {
        return $this->path;
    }

    /**
     * @param string|null $path
     * @return MessageAttachment
     */
    public function setPath(?string $path): MessageAttachment {
        $this->path = $path;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int {
        return $this->size;
    }

    /**
     * @param int|null $size
     * @return MessageAttachment
     */
    public function setSize(?int $size): MessageAttachment {
        $this->size = $size;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }
}