<?php

namespace Managers;

use Interfaces\StorageInterface;

use Predis\Client;

/**
 * Реализация хранилища на основе редиса
 *
 * Class StorageManager
 * @package Managers
 */
class StorageManager implements StorageInterface
{
    /**
     * Для хранения синглтона
     *
     * @var null
     */
    private static $instance = null;

    /**
     * Для хранения клиента редиса
     *
     * @var null|Client
     */
    private $client = null;

    protected function __construct()
    {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host'   => getenv('REDIS_HOST') ?: 'localhost',
            'port'   => getenv('REDIS_PORT') ?: 6379,
        ]);
    }

    protected function __clone()
    {

    }

    public static function getInstance(): StorageManager
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(string $key, $defaultValue = null)
    {
        $value = $this->client->get($key);

        if (is_null($value)) {
            return $defaultValue;
        }

        return $value;
    }

    public function set(string $key, $value)
    {
        $this->client->set($key, $value);
    }

    public function ping()
    {
        $this->client->ping('pong');
    }
}
