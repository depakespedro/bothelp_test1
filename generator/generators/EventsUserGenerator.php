<?php

namespace Generators;

use Interfaces\StorageInterface;
use Models\User;
use Models\Event;

/**
 * Class EventsUserGenerator
 * @package Generators
 */
class EventsUserGenerator
{
    /**
     * Генерирует пулл евентов для юзера
     *
     * @param User $user
     * @param StorageInterface $storage
     * @return array
     * @throws \Exception
     */
    static public function generate(User $user, StorageInterface $storage): array
    {
        $min = getenv('MIN_COUNT_EVENTS') ?: 1;
        $max = getenv('MAX_COUNT_EVENTS') ?: 5;

        $count = random_int($min, $max);

        $events = [];

        $lastId = $storage->get('last_id_event_' . $user->getId(), 0);
        $id = $lastId;

        for ($i = 0; $i < $count; $i++) {
            $id++;
            $events[] = new Event($id, $user);
        }

        $storage->set('last_id_event_' . $user->getId(), $id);

        return $events;
    }
}
