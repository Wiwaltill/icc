<?php

namespace App\Grouping;

use App\Entity\Message;
use SchoolIT\CommonBundle\Helper\DateHelper;

class MessageExpirationStrategy implements GroupingStrategyInterface {

    private $dateHelper;

    public function __construct(DateHelper $dateHelper) {
        $this->dateHelper = $dateHelper;
    }

    /**
     * @param Message $object
     * @return bool
     */
    public function computeKey($object) {
        return $this->dateHelper
            ->isBetween($this->dateHelper->getNow(), $object->getStartDate(), $object->getExpireDate());
    }

    /**
     * @param bool $keyA
     * @param bool $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA === $keyB;
    }

    /**
     * @param bool $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new MessageExpirationGroup($key);
    }
}