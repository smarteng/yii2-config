<?php

namespace smarteng\config\components;

use Yii;
use yii\base\Component;
use yii\di\Instance;

/**
 * Config component
 */
class Config extends Component implements ConfigInterface
{
    /**
     * @var ConfigInterface
     */
    public $provider;

    /**
     * The ID of the cache component
     * @var string
     */
    public $idCache = 'cache';

    /**
     * The key identifying the value to be cached
     * @var string
     */
    public $cacheKey = 'config.component';

    /**
     * The number of seconds in which the cached value will expire. 0 means never expire.
     * @var integer
     */
    public $cacheDuration = 0;

    /**
     * Config data
     * @var array
     */
    private $data = [];


    /**
     * @var \yii\caching\Cache
     */
    private $cache;

    /**
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->provider = Instance::ensure($this->provider);
        $this->cache = Instance::ensure($this->idCache);
        parent::init();
    }

    /**
     * Get data
     * @return array
     */
    public function getData()
    {
        if ($this->data === null) {
            $cache = $this->cache->get($this->cacheKey);
            if ($cache === false) {
                $this->data = $this->provider->getAll();
                $this->setCache();
            } else {
                $this->data = $cache;
            }
        }
        return $this->data;
    }

    /**
     * Set cache
     */
    private function setCache()
    {
        if ($this->cache !== null) {
            $this->cache->set($this->cacheKey, $this->data, $this->cacheDuration);
        }
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key, $default = '')
    {
        $data = $this->getData();

        return $data[$key] ?? $default;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->getData();
    }

    /**
     * @param $key
     * @param mixed $value
     *
     * @return mixed|void
     */
    public function set($key, $value)
    {
        $this->getData();
        $ret = $this->provider->set($key, $value);
        if ($ret) {
            $this->data[$key] = $value;
            $this->setCache();
        }
    }

    /**
     * @param array $items
     *
     * @return mixed|void
     */
    public function setAll(array $items)
    {
        $this->getData();
        $this->provider->setAll($items);
        foreach ($items as $key => $val) {
            $this->data[$key] = $val;
        }
        $this->setCache();
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        $data = $this->getData();
        return isset($data[$key]);
    }

    /**
     * @param array $keys
     *
     * @return string[]
     */
    public function getMultiply(array $keys)
    {
        $data = $this->getData();
        $ret = [];
        foreach ($keys as $k) {
            if (isset($data[$k])) {
                $ret[$k] = $data[$k];
            }
        }

        return $ret;
    }

    /**
     * Delete item
     *
     * @param string $key
     */
    public function delete($key)
    {
        $data = $this->getData();
        if (isset($data[$key])) {
            $this->provider->delete($key);
            unset($this->data[$key]);
            $this->setCache();
        }
    }

    /**
     * Delete all items
     */
    public function deleteAll()
    {
        $this->provider->deleteAll();
        $this->data = [];
        $this->cache->delete($this->cacheKey);
    }
}