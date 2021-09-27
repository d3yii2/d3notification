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

### notification model

Extend regular model and implement Notification interface
```php
class KpsSmgsNotification extends KpsSmgs implements Notification
{

    public const NOTIFICATION_NEW_ID = 1;
    public const NOTIFICATION_RESOLVED_ID = 2;
    public const NOTIFICATION_IGNORED_ID = 3;

    public const NOTIFICATION_TYPE_NO_DELIVERY = 1;
    public const NOTIFICATION_TYPE_MISMATCH_LOAD_STATION = 2;
    public const NOTIFICATION_TYPE_MISMATCH_END_STATION = 3;
    public const NOTIFICATION_TYPE_KPS_EXTRA_CAR = 4;
    public const NOTIFICATION_TYPE_DELIVERY_EXTRA_CAR = 5;
    public const NOTIFICATION_TYPE_DIFF_WEIGHT = 6;

    /** @var CmdDelivery */
    public $delivery;

    /** @var CmdDCar */
    public $deliveryCar;

    /** @var KpsSmgs */
    public $smgs;

    /** @var KpsSmgsWagon */
    public $smgsWagon;

    /** @var CmdStatus */
    public $deliveryStatus;
    /**
     * @var mixed|null
     */
    public $statusId;
    /**
     * @var mixed|null
     */
    public $typeId;


    public function getNotificationData(): array
    {
        $data = [];
        if($this->deliveryCar){
            $data['deliveryCarNumber'] = $this->deliveryCar->number;
        }
        if($this->deliveryStatus){
            $data['deliveryStatusType'] = $this->deliveryStatus->type;
            $data['deliveryStatusStation'] = TtStation::findOne($this->deliveryStatus->station_id)->name_ru;
        }

        if($this->delivery){
            $data['deliveryWeight'] = $this->delivery->weight;
            $data['deliverySmgsNumber'] = $this->delivery->delivery_note;
        }

        if($this->smgs){
            $data['kpsSmgsNumber'] = $this->smgs->number;
        }

        if($this->smgsWagon){
            $data['kpsCarNumber'] = $this->smgsWagon->number;
            $data['kpsCarWeight'] = $this->smgsWagon->weight;
        }
        return $data;
    }

    public function getNotificationKey(): int
    {
        if($this->delivery){
            return $this->delivery->id;
        }
        return 0;
    }
    public function getNotificationRecordId(): int
    {
        return $this->id;
    }

    public function getNotificationStatusList(): array
    {
        return [
            self::NOTIFICATION_NEW_ID => 'New',
            self::NOTIFICATION_RESOLVED_ID => 'Resolved',
            self::NOTIFICATION_IGNORED_ID => 'Ignored'
        ];
    }

    public function getNotificationTypeList(): array
    {
        return [
            self::NOTIFICATION_TYPE_NO_DELIVERY => 'Not Found Delivery record',
            self::NOTIFICATION_TYPE_MISMATCH_LOAD_STATION => 'Mismatch load station',
            self::NOTIFICATION_TYPE_MISMATCH_END_STATION => 'Mismatch end station',
            self::NOTIFICATION_TYPE_KPS_EXTRA_CAR => 'KPS Extra car',
            self::NOTIFICATION_TYPE_DELIVERY_EXTRA_CAR => 'Delivery Extra Car',
            self::NOTIFICATION_TYPE_DIFF_WEIGHT => 'Different weight',
        ];
    }

    public function getStatusId(): int
    {
        return $this->statusId;
    }

    public function getTypeId(): int
    {
        return $this->typeId;
    }
}


```

###Notification registration
```php
$logic = new NotificationLogic($companyId,$userId);
$notification = new KpsSmgsNotification();
$notification->attributes = $smgs->attributes;
$notification->typeId = KpsSmgsNotification::NOTIFICATION_TYPE_MISMATCH_END_STATION;
$notification->statusId = KpsSmgsNotification::NOTIFICATION_NEW_ID;
$notification->smgs = $smgs;
$notification->delivery = $delivery;
$notification->deliveryStatus = $statusEndStation;
$logic->register($notification);
```

###Notification registration by  form
To controlier add action

```php 
    public function actions()
    {
        return [
            'notification-create' => [
                'class' => CreateNotification::class,
                'notificationModelClass' => MTaskNotification::class,
                'typeId' => MTaskNotification::NOTIFICATION_TYPE_OTHER,
                'backUrl' => ['view','id' => '@id'],
                'notesList' => [
                    'aaa1',
                    'aaa2',
                 ]
            ]
        ];
    }    
```

### widget for model actions
show all model record notifications
```php 
        echo ModelNotifications::widget([
            'modelClass' => MTaskNotification::class,
            'modelRecordId' => $model->id
        ]);
```