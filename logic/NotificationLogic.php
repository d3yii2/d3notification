<?php

namespace d3yii2\d3notification\logic;



use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\dictionaries\D3nTypeDictionary;
use d3yii2\d3notification\interfaces\Notification;
use d3yii2\d3notification\models\D3nNotification;
use d3yii2\d3notification\models\D3nStatusHistory;
use yii\base\BaseObject;
use yii\helpers\Json;


class NotificationLogic extends BaseObject
{
    /** @var int */
    private $sysCompanyId;

    /** @var int */
    private $userId;

    /**
     * NotificationLogic constructor.
     * @param int $sysCompanyId
     * @param int $userId
     */
    public function __construct(int $sysCompanyId, int $userId)
    {
        parent::__construct();
        $this->sysCompanyId = $sysCompanyId;
        $this->userId = $userId;
    }

    /**
     * @param Notification $notificationModel
     * @throws D3ActiveRecordException
     */
    public function register(Notification $notificationModel): void
    {
        $idByClassName = SysModelsDictionary::getIdByClassName(get_class($notificationModel));
        if($this->getNotifications($notificationModel)){
            return;
        }

        $model = new D3nNotification();
        $model->sys_company_id = $this->sysCompanyId;
        $model->time = date('Y-m-d H:i:s');
        $model->sys_model_id = $idByClassName;
        $model->model_record_id = $notificationModel->getNotificationRecordId();
        $model->key = $notificationModel->getNotificationKey();
        $model->type_id = D3nTypeDictionary::getIdByNotificationType($model->sys_model_id,$notificationModel);
        $model->status_id = D3nStatusDictionary::getIdByNotificationStatus($model->sys_model_id,$notificationModel);
        $model->data = Json::encode($notificationModel->getNotificationData());
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

    /**
     * @param Notification $notificationModel
     * @throws D3ActiveRecordException
     */
    public function changeStatus(Notification $notificationModel): void
   {

        if(!$modelList = $this->getNotifications($notificationModel)){
            $this->register($notificationModel);
            return;
        }
        foreach($modelList as $model) {
            $model->status_id = D3nStatusDictionary::getIdByNotificationStatus($model->sys_model_id, $notificationModel);
            if (!$model->save()) {
                throw new D3ActiveRecordException($model);
            }

            $this->saveStatusHistory($model);
        }
    }

    /**
     * @param Notification $notificationModel
     * @return D3nNotification[]
     * @throws D3ActiveRecordException
     */
    public function getNotifications(Notification $notificationModel): array
    {
        $idByClassName = SysModelsDictionary::getIdByClassName(get_class($notificationModel));
        return D3nNotification::findAll([
            'sys_company_id' => $this->sysCompanyId,
            'sys_model_id' => $idByClassName,
            'model_record_id' => $notificationModel->getNotificationRecordId(),
            'key' => $notificationModel->getNotificationKey(),
            'type_id' => D3nTypeDictionary::getIdByNotificationType($idByClassName,$notificationModel),
            'status_id' => $notificationModel->getNotificationStatusNewId()
        ]);
    }

    /**
     * @param string $notificationModelClass
     * @param int $statusId
     * @return D3nNotification[]
     * @throws D3ActiveRecordException
     */
    public function getNotificationList(string $notificationModelClass, int $statusId): array
    {
        $idByClassName = SysModelsDictionary::getIdByClassName($notificationModelClass);
        return D3nNotification::findAll([
            'sys_company_id' => $this->sysCompanyId,
            'sys_model_id' => $idByClassName,
            'status_id' => D3nStatusDictionary::getIdByStatusById($idByClassName,$statusId),
        ]);
    }

}
