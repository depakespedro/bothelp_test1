<?php

namespace Interfaces;

/**
 * Интерфейс для взаимодействия с хранилищем ключ-значение
 *
 * Interface StorageInterface
 * @package Interfaces
 */
interface StorageInterface
{
    /**
     * Получить значение по ключу
     *
     * @param string $key
     * @param null $defaultValue
     * @return mixed
     */
    public function get(string $key, $defaultValue = null);

    /**
     * Установить значение по ключу
     *
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value);

    /**
     * Проверка коннекта к хранилищу
     *
     * @return mixed
     */
    public function ping();
}
