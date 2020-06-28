<?php

namespace Generators;

use Models\User;

/**
 * Class UsersGenerator
 * @package Generators
 */
class UsersGenerator
{
    /**
     * Генерирует юзера
     *
     * @return User
     * @throws \Exception
     */
    static public function generate(): User
    {
        $maxId = getenv('MAX_COUNT_USERS') ?: 1000;

        $id = random_int(1, $maxId);

        return new User($id);
    }
}
