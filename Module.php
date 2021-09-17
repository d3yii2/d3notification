<?php

namespace d3yii2\d3notification;

use Yii;
use d3system\yii2\base\D3Module;

class Module extends D3Module
{
    public $controllerNamespace = 'd3yii2\d3notification\controllers';

    public $notificationModels = [];
    public $statusId;

    /**
     * get all notification modules statuses list for using in grid ass active
     * @return int[]
     */
    public function getActualStatusesIdList(): array
    {
        $list = [];
        foreach($this->notificationModels as $notificationModel){
            if(method_exists($notificationModel,'getNotificationActualStatuses')){
                foreach($notificationModel::getNotificationActualStatuses() as $statusId){
                    $list[] = $statusId;
                }
            }
        }
        return $list;
    }

    public function getLabel(): string
    {
        return Yii::t('d3notification','Notifications');
    }
}
