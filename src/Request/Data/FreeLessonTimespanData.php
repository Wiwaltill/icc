<?php

namespace App\Request\Data;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class FreeLessonTimespanData {

    /**
     * @Serializer\SerializedName("date")
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\NotNull()
     * @var DateTime|null
     */
    private $date;

    /**
     * @Serializer\SerializedName("start")
     * @Serializer\Type("int")
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\NotNull()
     * @var int|null
     */
    private $start;

    /**
     * @Serializer\SerializedName("end")
     * @Serializer\Type("int")
     * @Assert\GreaterThanOrEqual(propertyPath="start")
     * @Assert\NotNull()
     * @var int|null
     */
    private $end;

    /**
     * @param DateTime|null $date
     * @return FreeLessonTimespanData
     */
    public function setDate(?DateTime $date): FreeLessonTimespanData {
        $this->date = $date;
        return $this;
    }

    /**
     * @param int|null $start
     * @return FreeLessonTimespanData
     */
    public function setStart(?int $start): FreeLessonTimespanData {
        $this->start = $start;
        return $this;
    }

    /**
     * @param int|null $end
     * @return FreeLessonTimespanData
     */
    public function setEnd(?int $end): FreeLessonTimespanData {
        $this->end = $end;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @return int|null
     */
    public function getStart(): ?int {
        return $this->start;
    }

    /**
     * @return int|null
     */
    public function getEnd(): ?int {
        return $this->end;
    }
}