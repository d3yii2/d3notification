<?php

namespace d3yii2\d3notification\models;

use d3system\dictionaries\SysModelsDictionary;
use d3system\models\SysModels;
use \d3yii2\d3notification\models\base\D3nNotification as BaseD3nNotification;

/**
 * This is the model class for table "d3n_notification".
 */
class D3nNotification extends BaseD3nNotification
{
    private $model;

    public function getNotificationModel()
    {
        if($this->model !== null){
            return $this->model;
        }
        /** @var  $className string*/
        if(!$className = SysModelsDictionary::getClassList()[$this->sys_model_id]){
            return $this->model = false;
        }
        return $this->model = $className::findOne($this->model_record_id);

    }
}
