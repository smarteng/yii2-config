Yii2 Config
======
Manage configuration from database

Installation
------------

Either run

```sh
php composer.phar require --prefer-dist smarteng/yii2-config "*"
```

or add

```
"smarteng/yii2-config": "*"
```

to the require section of your `composer.json` file.



### Two

Applying migrations

```
yii migrate --migrationPath=@vendor/smarteng/yii2-config/migrations
```

Configuration
-------------

In configuration file
```php
'components' => [
    'config' => [
        'class' => '\smarteng\config\components\Config',
        'provider' => [
            'class' => '\smarteng\config\providers\DbProvider',
            'tableName' => '{{%config}}',  // by default
            'keyColumn' => 'key',                 // by default
            'valueColumn' => 'value',             // by default
        ]
    ],
    ...
]
```
Create own provider
--------------------
1. Create Class for provider
2. Implement `\smarteng\config\components\ConfigInterface`
3. Change in the configuration file on your provider

Usage
-----

Db provider example
```php
$isSet = \Yii::$app->config->set('commission', '10');   // can throw an exception
$isSet = \Yii::$app->config->safeSet('commission', '10');   // return false if something went wrong

$isSet = \Yii::$app->config->exists('commission');      // return true if key exists
$value = \Yii::$app->config->get('commission');         // return '10';
```

Uninstall
------------

Applying migrations

```
yii migrate/down --migrationPath=@vendor/smarteng/yii2-config/migrations
```