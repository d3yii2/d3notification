#Notification"

## Features
For active records register events (time,status, notification class, user). Status can be changed.
Collect status history.  

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ composer require d3yii2/d3notification "*"
```

or add

```
"d3yii2/d3notification": "*"
```

to the `require` section of your `composer.json` file.

## To console config add migration
```php
return [
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@vendor/d3yii2/d3notification/migrations',
            ]
        ],
    ]
];   


```

## DB

![DB strukture](https://github.com/d3yii2/d3notification/blob/master/doc/DbSchema.png)

## Methods


## Usage

## Examples
