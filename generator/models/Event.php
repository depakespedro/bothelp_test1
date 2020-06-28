<?php

namespace Models;

/**
 * Class Event
 * @package Models
 */
class Event
{
    private $id = null;
    private $userId = null;

    public function __construct(int $id, User $user)
    {
        $this->id = $id;
        $this->userId = $user->getId();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    private function __toString()
    {
        return json_encode([
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
        ]);
    }
}
