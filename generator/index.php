<?php

require __DIR__ . '/vendor/autoload.php';

use Tarantool\Client\Client;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Options;

use Generators\UsersGenerator;
use Generators\EventsUserGenerator;
use Managers\StorageManager;

/**
 * Инициализация очереди тарантула
 *
 * @return Queue
 * @throws Exception
 */
function initTarantoolQueue(): Queue
{
    $tarantoolHost = getenv('TARANTOOL_HOST') ?: 'localhost';
    $tarantoolPort = getenv('TARANTOOL_PORT') ?: 3301;

    $uri = "tcp://$tarantoolHost:$tarantoolPort";

    $tarantoolClient = Client::fromOptions([
        'uri' => $uri,
    ]);

    try {
        $tarantoolClient->ping();
    } catch (Exception $exception) {
        throw new Exception('Tarantool client is not connected');
    }

    $queue = new Queue($tarantoolClient, 'events');

    return $queue;
}

function main (): void
{
    $storage = StorageManager::getInstance();
    try {
        $storage->ping();
    } catch (Exception $exception) {
        throw new Exception('StorageManager is not connected');
    }

    try {
        $queue = initTarantoolQueue();
    } catch (Exception $exception) {
        throw new Exception('Error init tarantool queue or error connect in tarantool');
    }

    while (true) {
        $user = UsersGenerator::generate();
        $events = EventsUserGenerator::generate($user, $storage);

        foreach ($events as $event) {
            echo $event . PHP_EOL;
            // фишка очереди типа UTUBE - это очередь в очереди, позволяет грамотнее раскидывать эвенты для юзеров
            $queue->put((string) $event, [Options::UTUBE => $user->getId()]);
        }

        sleep(getenv('SLEEP_GENERATOR') ?: 0.5);
    }
}

main();
