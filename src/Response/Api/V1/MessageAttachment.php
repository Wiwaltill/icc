<?php

namespace App\Response\Api\V1;

use DateTime;
use DateTimeImmutable;
use App\Entity\MessageAttachment as MessageAttachmentEntity;
use JMS\Serializer\Annotation as Serializer;

class MessageAttachment {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("filename")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $filename;

    /**
     * @Serializer\SerializedName("size")
     * @Serializer\Type("int")
     *
     * @var int
     */
    private $size;

    /**
     * @Serializer\SerializedName("updated_at")
     * @Serializer\Type("DateTimeImmutable")
     *
     * @var DateTime
     */
    private $updatedAt;

    /**
     * @return string
     */
    public function getFilename(): string {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return MessageAttachment
     */
    public function setFilename(string $filename): MessageAttachment {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int {
        return $this->size;
    }

    /**
     * @param int $size
     * @return MessageAttachment
     */
    public function setSize(int $size): MessageAttachment {
        $this->size = $size;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return MessageAttachment
     */
    public function setUpdatedAt(DateTime $updatedAt): MessageAttachment {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public static function fromEntity(MessageAttachmentEntity $messageAttachmentEntity): self {
        return (new self())
            ->setUuid($messageAttachmentEntity->getUuid())
            ->setFilename($messageAttachmentEntity->getFilename())
            ->setSize($messageAttachmentEntity->getSize())
            ->setUpdatedAt($messageAttachmentEntity->getUpdatedAt());
    }
}