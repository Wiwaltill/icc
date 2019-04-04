<?php

namespace App\Grouping;

use App\Entity\Document;
use App\Entity\DocumentVisibility;
use App\Entity\UserType;

class DocumentUserTypeStrategy implements GroupingStrategyInterface {

    /**
     * @param Document $document
     * @return UserType[]
     */
    public function computeKey($document) {
        return array_map(function(DocumentVisibility $visibility) {
            return $visibility->getUserType();
        }, $document->getVisibilities()->toArray());
    }

    /**
     * @param UserType $keyA
     * @param UserType $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA->equals($keyB);
    }

    /**
     * @param UserType $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new DocumentUserTypeGroup($key);
    }
}