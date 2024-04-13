<?php

namespace d3yii2\d3notification\dictionaries;

use d3yii2\d3notification\interfaces\Notification;
use Yii;
use d3yii2\d3notification\models\D3nStatus;
use yii\helpers\ArrayHelper;
use d3system\exceptions\D3ActiveRecordException;

class D3nStatusDictionary{

    private const CACHE_KEY_LIST = 'D3nStatusDictionaryList';
    private const CACHE_KEY_NOTIFICATION_LIST = 'D3nStatusDictionaryNotificationList1';

    /**
     * @throws D3ActiveRecordException
     */
    public static function getIdByNotificationStatus(
        int $sysModelId,
        Notification $notification,
        int $statusId = null
    ): int
    {
        if (!$statusId) {
            $statusId = $notification->getNotificationStatusId();
        }
        $key = $sysModelId . '-' . $statusId;
        if($id = self::getListByNotification()[$key]??0){
            return $id;
        }
        $model = new D3nStatus();
        $model->sys_model_id = $sysModelId;
        $model->status_id = $statusId;
        $model->label = $notification->getNotificationStatusList()[$statusId]??'-';
        if(!$model->save()){
            throw new D3ActiveRecordException($model);
        }
        return $model->id;

    }

    public static function getIdByStatusById(
        int $sysModelId,
        int $statusId
    ): int
    {
        return self::getListByNotification()[$sysModelId . '-' . $statusId];
    }

    public static function getList(array $translations = []): array
    {
        return Yii::$app->cache->getOrSet(
            self::CACHE_KEY_NOTIFICATION_LIST,
            static function () use ($translations)  {
                $list = ArrayHelper::map(
                    D3nStatus::find()
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
                if ($translations) {
                    foreach ($list as $id => $name) {
                        foreach ($translations as $translation) {
                            $newName = Yii::t($translation, $name);
                            if ($newName !== $name) {
                                $list[$id] = $newName;
                                break;
                            }
                        }
                    }
                }
                return $list;
            },
            3600
        );
    }

    public static function getListByNotification(): array
    {
        return Yii::$app->cache->getOrSet(
            self::CACHE_KEY_LIST,
            static function () {
                return ArrayHelper::map(
                    D3nStatus::find()
                    ->select([
                        'id' => 'CONCAT(sys_model_id,\'-\',status_id)',
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
