<?php

namespace smarteng\config\providers;

use smarteng\config\components\ConfigInterface;
use yii\base\Component;
use yii\db\Connection;
use yii\db\Exception;
use yii\db\Query;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

/**
 * Class DbProvider
 * @package ymaker\configuration\providers
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
class DbProvider extends Component implements ConfigInterface
{
    /**
     * @var Connection
     */
    public $db = 'db';
    public $tableName = "{{%config}}";
    public $keyColumn = "key";
    public $valueColumn = "value";

    /**
     * Init
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db);
    }

    /**
     * get value from configuration by key
     *
     * @param $key
     * @param string $default
     *
     * @return mixed
     */
    public function get($key, $default = '')
    {
        $result = $this->findOneByKey($key, $this->valueColumn);

        return $result[$this->valueColumn] ?? $default;
    }

    /**
     * Returns all parameters
     *
     * @return array
     */
    public function getAll()
    {
        $ret = [];
        $item_list = (new Query())
            ->select([$this->keyColumn, $this->valueColumn])
            ->from($this->tableName)
            ->all();
        if ($item_list) {
            foreach ($item_list as $key => $item) {
                $ret[$item[$this->keyColumn]] = $item[$this->valueColumn];
            }
        }

        return $ret;
    }

    /**
     * set value to configuration
     *
     * @param $key
     * @param mixed $value
     *
     * @return bool|mixed
     * @throws Exception
     */
    public function set($key, $value)
    {
        $query = new Query();
        $command = $query->createCommand($this->db);
        if ($this->exists($key)) {
            $command = $command->update($this->tableName, [
                $this->keyColumn => $key,
                $this->valueColumn => $value,
            ],
                [$this->keyColumn => $key]
            );
        } else {
            $command = $command->insert($this->tableName, [
                $this->keyColumn => $key,
                $this->valueColumn => $value,
            ]);
        }

        return $command->execute();
    }

    /**
     * @param array $items
     *
     * @return void
     * @throws Exception
     */
    public function setAll(array $items)
    {
        foreach ($items as $index => $item) {
            $this->set($item[$this->keyColumn], $item[$this->valueColumn]);
        }

    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        return (new Query())
            ->select($this->keyColumn)
            ->from($this->tableName)
            ->where([$this->keyColumn => $key])
            ->exists($this->db);
    }

    /**
     *
     * @param $key
     * @param array $columns
     *
     * @return array|bool
     */
    private function findOneByKey($key, $columns = [])
    {
        return (new Query())
            ->select($columns)
            ->from($this->tableName)
            ->where([
                $this->keyColumn => $key
            ])
            ->limit(1)
            ->one($this->db);
    }

    /**
     * @param array $keys
     *
     * @return string[]
     */
    public function getMultiply(array $keys)
    {
        $valuesQuery = (new Query())
            ->select([$this->keyColumn, $this->valueColumn])
            ->from($this->tableName)
            ->where([
                $this->keyColumn => $keys
            ]);
        $values = ArrayHelper::map($valuesQuery->all($this->db), $this->keyColumn, $this->valueColumn);
        return $values;
    }

    /**
     * Delete all items
     *
     * @return \yii\db\Command
     */
    public function deleteAll()
    {
        return $this->db->createCommand()->truncateTable($this->tableName);
    }

    /**
     * @param string $key
     *
     * @return bool|\yii\db\Command
     */
    public function delete($key)
    {
        if ($this->exists($key)) {
            return $this->db->createCommand()
                ->delete($this->tableName, $this->keyColumn.'=:key', [':key' => $key]);
        }
        return true;
    }

}