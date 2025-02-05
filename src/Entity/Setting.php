<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @UniqueEntity(fields={"key"})
 */
class Setting {

    /**
     * @ORM\Id()
     * @ORM\Column(name="`key`", type="string", unique=true)
     * @Assert\NotBlank()
     * @var string
     */
    private $key;

    /**
     * @ORM\Column(type="object")
     * @var mixed
     */
    private $value = null;

    /**
     * @return string
     */
    public function getKey(): string {
        return $this->key;
    }

    /**
     * @param string $key
     * @return Setting
     */
    public function setKey(string $key): Setting {
        $this->key = $key;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Setting
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }
}