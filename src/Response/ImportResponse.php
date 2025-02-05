<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

/**
 * Indicates a successful import. For now, only the number of added, updated and removed entities is serialized to JSON.
 */
class ImportResponse {

    /**
     * Added entities.
     * @Serializer\Exclude()
     * @var object[]
     */
    private $added;

    /**
     * Updated entities.
     * @Serializer\Exclude()
     * @var object[]
     */
    private $updated;

    /**
     * Removed entities.
     * @Serializer\Exclude()
     * @var object[]
     */
    private $removed;

    /**
     * Ignored entities.
     *
     * @var object[]
     */
    private $ignored;

    /**
     * Number of added entities.
     *
     * @Serializer\Type("int")
     * @var int
     */
    private $addedCount;

    /**
     * Number of updated entities.
     *
     * @Serializer\Type("int")
     * @var int
     */
    private $updatedCount;

    /**
     * Number of removed entities.
     *
     * @Serializer\Type("int")
     * @var int
     */
    private $removedCount;

    /**
     * Number of ignored entities.
     * @Serializer\Type("int")
     * @var int
     */
    private $ignoredCount;

    public function __construct(array $added, array $updated, array $removed, array $ignored) {
        $this->added = $added;
        $this->updated = $updated;
        $this->removed = $removed;
        $this->ignored = $ignored;

        $this->addedCount = count($added);
        $this->updatedCount = count($updated);
        $this->removedCount = count($removed);
        $this->ignoredCount = count($ignored);
    }

    /**
     * @return object[]
     */
    public function getAdded() {
        return $this->added;
    }

    /**
     * @return object[]
     */
    public function getUpdated() {
        return $this->updated;
    }

    /**
     * @return object[]
     */
    public function getRemoved() {
        return $this->removed;
    }

    /**
     * @return object[]
     */
    public function getIgnored(): array {
        return $this->ignored;
    }

    /**
     * @return int
     */
    public function getAddedCount(): int {
        return $this->addedCount;
    }

    /**
     * @return int
     */
    public function getUpdatedCount(): int {
        return $this->updatedCount;
    }

    /**
     * @return int
     */
    public function getRemovedCount(): int {
        return $this->removedCount;
    }

    /**
     * @return int
     */
    public function getIgnoredCount(): int {
        return $this->ignoredCount;
    }
}