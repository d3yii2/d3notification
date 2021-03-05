<?php

namespace d3yii2\d3notification\dictionaries;

use d3yii2\d3notification\interfaces\Notification;
use d3yii2\d3notification\models\D3nType;
use Yii;
use yii\helpers\ArrayHelper;
use d3system\exceptions\D3ActiveRecordException;

class D3nTypeDictionary{

    private const CACHE_KEY_LIST = 'D3nTypeDictionaryList';
    private const CACHE_KEY_NOTIFICATION_LIST = 'D3nTypeDictionaryNotificationList';

    public static function getIdByNotificationType(
        int $sysModelId,
        Notification $notification
    ): int
    {

        $typeId = $notification->getNotificationTypeId();
        $key = $sysModelId . '-' . $typeId;
        if($id = self::getListByNotification()[$key]??0){
            return $id;
        }
        $model = new D3nType();
        $model->sys_model_id = $sysModelId;
        $model->type_id = $typeId;
        $model->label = $notification->getNotificationTypeList()[$typeId]??'-';
        if(!$model->save()){
            throw new D3ActiveRecordException($model);
        }
        return $model->id;

    }

    public static function getIdByType(
        int $sysModelId,
        int $typeId
    ): int
    {
        $key = $sysModelId . '-' . $typeId;
        return self::getListByNotification()[$key]??false;
    }

    public static function getList(): array
    {
        return Yii::$app->cache->getOrSet(
            self::CACHE_KEY_NOTIFICATION_LIST,
            static function () {
                return ArrayHelper::map(
                    D3nType::find()
                    ->select([
                        'id' => 'id',
                        'name' => 'label',
                        //'name' => 'CONCAT(code,\' \',name)'
                    ])
                    ->orderBy([
                        'id' => SORT_ASC,
                    ])
                    ->asArray()
                    ->all()
                ,
                'id',
                'name'
                );
            }
        );
    }

    public static function getListByNotification(): array
    {
        return Yii::$app->cache->getOrSet(
            self::CACHE_KEY_LIST,
            static function () {
                return ArrayHelper::map(
                    D3nType::find()
                    ->select([
                        'id' => 'CONCAT(sys_model_id,\'-\',type_id)',
                        'name' => 'id',
                        //'name' => 'CONCAT(code,\' \',name)'
                    ])
                    ->orderBy([
                        'id' => SORT_ASC,
                    ])
                    ->asArray()
                    ->all()
                ,
                'id',
                'name'
                );
            }
        );
    }

    public static function clearCache(): void
    {
        Yii::$app->cache->delete(self::CACHE_KEY_LIST);
        Yii::$app->cache->delete(self::CACHE_KEY_NOTIFICATION_LIST);
    }
}
