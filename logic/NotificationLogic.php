<?php

namespace d3yii2\d3notification\logic;



use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\interfaces\Notification;
use d3yii2\d3notification\models\D3nNotification;
use d3yii2\d3notification\models\D3nStatusHistory;
use Yii;
use yii\base\BaseObject;


class NotificationLogic extends BaseObject
{
    /** @var int */
    private $sysCompanyId;

    /** @var int */
    private $userId;

    /**
     * @param Notification $notificationModel
     * @throws D3ActiveRecordException
     */
    public function register(Notification $notificationModel): void
    {
        $model = new D3nNotification();
        $model->sys_company_id = $this->sysCompanyId;
        $model->time = date('Y-m-d H:i:s');
        $model->sys_model_id = Yii::$app->sysModel->getIdByClassName(get_class($notificationModel));
        $model->model_record_id = $notificationModel->getNotificationRecordId();
        $model->key = $notificationModel->getNotificationKey();
        $model->status_id = D3nStatusDictionary::getIdByNotificationStatus($model->sys_model_id,$notificationModel);
        $model->data = $notificationModel->getNotificationData();
        if(!$model->save()){
            throw new D3ActiveRecordException($model);
        }

        $this->saveStatusHistory($model);

    }

    /**
     * @param D3nNotification $model
     * @throws D3ActiveRecordException
     */
    private function saveStatusHistory(D3nNotification $model): void
    {
        $statusHistory = new D3nStatusHistory();
        $statusHistory->notification_id = $model->id;
        $statusHistory->status_id = $model->status_id;
        $statusHistory->time = $model->time;
        $statusHistory->user_id = $this->userId;
        if (!$statusHistory->save()) {
            throw new D3ActiveRecordException($statusHistory);
        }
    }

    public function changeStatus(Notification $notificationModel): void
    {
        if(!$model = D3nNotification::findOne([
            'sys_model_id' => Yii::$app->sysModel->getIdByClassName(get_class($notificationModel)),
            'model_record_id' => $notificationModel->getNotificationRecordId()
        ])){
            $this->register($notificationModel);
            return;
        }
        $model->status_id = D3nStatusDictionary::getIdByNotificationStatus($model->sys_model_id,$notificationModel);
        if(!$model->save()){
            throw new D3ActiveRecordException($model);
        }

        $this->saveStatusHistory($model);
    }

}
