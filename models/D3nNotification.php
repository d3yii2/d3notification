<?php

namespace d3yii2\d3notification\models;

use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3notification\interfaces\Notification;
use \d3yii2\d3notification\models\base\D3nNotification as BaseD3nNotification;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * This is the model class for table "d3n_notification".
 */
class D3nNotification extends BaseD3nNotification
{

    /** @var Notification */
    private $model;

    /**
     * @return bool|Notification
     * @throws Exception
     */
    public function getNotificationModel()
    {
        if($this->model !== null){
            return $this->model;
        }
        /** @var  $className string*/
        if(!$className = SysModelsDictionary::getClassList()[$this->sys_model_id]){
            return $this->model = false;
        }

        /** @var $className ActiveRecord */
        if(!$this->model = $className::findOne($this->model_record_id)){
            throw new Exception('Can not find ' . $className . ' record ' . $this->model_record_id);
        }
        $this->model->loadNotificationData($this);

        return $this->model;

    }

    public function delete()
    {
        foreach ($this->d3nStatusHistories as $history){
            $history->delete();
        }
        return parent::delete();
    }
}
