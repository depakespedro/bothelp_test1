<?php

namespace Models;

/**
 * Class User
 * @package Models
 */
class User
{
    private $id = null;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
