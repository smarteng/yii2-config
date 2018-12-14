<?php
use yii\db\Schema;
use yii\db\Migration;

/**
 * Install
 * php yii migrate --migrationPath=@vendor/smarteng/yii2-config/migrations
 * 
 * Uninstall
 * php yii migrate/down --migrationPath=@vendor/smarteng/yii2-config/migrations
 */
class m141028_111823_config extends Migration
{
    public $tableName = '{{%config}}';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'key' => $this->string(50)->notNull()->unique(),
            'value' => $this->text()->null(),
        ], $tableOptions);
        $this->createIndex(
            $this->indexName,
            $this->tableName,
            'key',
            true);
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
