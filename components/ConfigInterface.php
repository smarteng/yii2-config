<?php

namespace smarteng\config\components;

interface ConfigInterface
{
    /**
     * Get config var
     *
     * @param $name
     * @param string $default
     *
     * @return mixed
     *
     */
    public function get($name, $default = '');

    /**
     * @param array $keys
     *
     * @return string[]
     */
    public function getMultiply(array $keys);

    /**
     * Returns all parameters
     * @return array
     */
    public function getAll();

    /**
     * Set config vars
     *
     * @param $name
     * @param mixed $value
     *
     * @return mixed
     */
    public function set($name, $value);

    /**
     * Set all
     *
     * @param array $items
     *
     * @return mixed
     */
    public function setAll(array $items);

    /**
     * Delete parameter
     *
     * @param string $name
     */
    public function delete($name);

    /**
     * Remove all data
     */
    public function deleteAll();

    /**
     *  return true if this key exist
     *
     * @param $key string
     *
     * @return boolean returns true if the key has been set
     */
    public function exists($key);

}