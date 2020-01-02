<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class InfotextsData {

    /**
     * @Serializer\Type("array<App\Request\Data\InfotextData>")
     * @Assert\Valid()
     * @var InfotextData[]
     */
    private $infotexts;

    /**
     * @return InfotextData[]
     */
    public function getInfotexts(): array {
        return $this->infotexts;
    }

    /**
     * @param InfotextData[] $infotexts
     * @return InfotextsData
     */
    public function setInfotexts(array $infotexts): InfotextsData {
        $this->infotexts = $infotexts;
        return $this;
    }

}