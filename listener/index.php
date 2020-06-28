<?php

require __DIR__ . '/vendor/autoload.php';

use Tarantool\Client\Client;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Options;
use Managers\StorageManager;

/**
 * Имитация для обоработки события для юзера
 *
 * @param int $eventId
 * @param int $userId
 */
function handlerEvent(int $eventId, int $userId): void
{
    echo "Work user: $userId event: $eventId" . PHP_EOL;
    sleep(1);
}

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

function main()
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
        try {
            $task = $queue->take();
        } catch (Exception $exception) {
            echo 'Reconnect tarantool' . PHP_EOL;

            try {
                $queue = initTarantoolQueue();
            } catch (Exception $exception) {
                throw new Exception('Error init tarantool queue or error connect in tarantool');
            }

            continue;
        }

        $data = json_decode($task->getData());

        $userId = $data->userId;
        $eventId = $data->id;

        // если пользовательское событие уже обратывается, то откладываем следующую задачу, дабы сохранить очередность
        $isBusyUser = $storage->get('is_busy_user_' . $userId, false);

        if ($isBusyUser) {
            $queue->release($task->getId(), [Options::DELAY => 2.0]);
            echo "User: $userId, event: $eventId is busy" . PHP_EOL;
            sleep(0.1);
            continue;
        }

        // блокируем пользователя, на время обработки события
        $storage->set('is_busy_user_' . $userId, true);

        handlerEvent($eventId, $userId);

        // разблокируем пользователя
        $storage->set('is_busy_user_' . $userId, false);

        $queue->ack($task->getId());
    }
}

main();

return;
