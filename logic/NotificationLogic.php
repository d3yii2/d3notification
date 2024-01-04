<?php

namespace d3yii2\d3notification\logic;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\dictionaries\D3nTypeDictionary;
use d3yii2\d3notification\interfaces\Notification;
use d3yii2\d3notification\models\D3nNotification;
use d3yii2\d3notification\models\D3nStatusHistory;
use Yii;
use yii\base\BaseObject;
use yii\base\UserException;
use yii\helpers\Json;

class NotificationLogic extends BaseObject
{
    private ?int $sysCompanyId = null;
    private ?int $userId = null;

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
     * @param string|null $notes
     * @param string|null $userNotes
     * @throws D3ActiveRecordException
     * @throws UserException
     */
    public function register(
        Notification $notificationModel,
        string $notes = null,
        string $userNotes = null
    ): void {
        $idByClassName = SysModelsDictionary::getIdByClassName(get_class($notificationModel));
        if ($this->getNotifications($notificationModel, $notes)) {
            throw new UserException(Yii::t('d3notification', 'An alert of this type has already been added'));
        }

        $model = new D3nNotification();
        $model->sys_company_id = $this->sysCompanyId;
        $model->time = date('Y-m-d H:i:s');
        $model->sys_model_id = $idByClassName;
        $model->model_record_id = $notificationModel->getNotificationRecordId();
        $model->key = $notificationModel->getNotificationKey();
        $model->type_id = D3nTypeDictionary::getIdByNotificationType($model->sys_model_id, $notificationModel);
        $model->status_id = D3nStatusDictionary::getIdByNotificationStatus($model->sys_model_id, $notificationModel);
        if ($notes) {
            $model->notes = $notes;
        }
        $notificationData = $notificationModel->getNotificationData();
        if (Yii::$app->has('user') && Yii::$app->user !== null) {
            $notificationData['reportUserId'] = Yii::$app->user->id;
        }
        $model->data = Json::encode($notificationData);
        if (!$model->save()) {
            throw new D3ActiveRecordException($model);
        }

        $this->saveStatusHistory($model, $userNotes);
    }

    /**
     * @param D3nNotification $model
     * @param string|null $notes
     * @throws D3ActiveRecordException
     */
    private function saveStatusHistory(D3nNotification $model, string $notes = null): void
    {
        $statusHistory = new D3nStatusHistory();
        $statusHistory->notification_id = $model->id;
        $statusHistory->status_id = $model->status_id;
        $statusHistory->time = $model->time;
        $statusHistory->user_id = $this->userId;
        $statusHistory->notes = $notes;
        if (!$statusHistory->save()) {
            throw new D3ActiveRecordException($statusHistory);
        }
    }

    /**
     * @param Notification $notificationModel
     * @param int|null $prevStatusId
     * @throws D3ActiveRecordException
     * @throws UserException
     */
    public function changeStatus(Notification $notificationModel, int $prevStatusId = null): void
    {

        if (!$modelList = $this->getNotifications($notificationModel,null, $prevStatusId)) {
            $this->register($notificationModel);
            return;
        }
        foreach ($modelList as $model) {
            $model->status_id = D3nStatusDictionary::getIdByNotificationStatus($model->sys_model_id, $notificationModel);
            if (!$model->save()) {
                throw new D3ActiveRecordException($model);
            }

            $this->saveStatusHistory($model);
        }
    }

    /**
     * @param Notification $notificationModel
     * @param string|null $notes
     * @return D3nNotification[]
     * @throws D3ActiveRecordException
     */
    public function getNotifications(Notification $notificationModel, string $notes = null, int $prevStatusId = null): array
    {
        $idByClassName = SysModelsDictionary::getIdByClassName(get_class($notificationModel));
        if (!$prevStatusId) {
            $prevStatusId = $notificationModel->getNotificationStatusNewId();
        }
        return D3nNotification::findAll([
            'sys_company_id' => $this->sysCompanyId,
            'sys_model_id' => $idByClassName,
            'model_record_id' => $notificationModel->getNotificationRecordId(),
            'key' => $notificationModel->getNotificationKey(),
            'type_id' => D3nTypeDictionary::getIdByNotificationType($idByClassName, $notificationModel),
            'status_id' => $prevStatusId,
            'notes' => $notes
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
            'status_id' => D3nStatusDictionary::getIdByStatusById($idByClassName, $statusId)
        ]);
    }


    /**
     * get model record notifications
     *
     * @param string $modelClass
     * @param int $modelRecordId
     * @param int $statusId
     * @param int|null $typeId
     * @return D3nNotification[]
     * @throws D3ActiveRecordException
     */
    public function getModelRecordNotificationList(
        string $modelClass,
        int $modelRecordId,
        int $statusId,
        int $typeId = null
    ): array {
        $idByClassName = SysModelsDictionary::getIdByClassName($modelClass);
        $condition = [
            'sys_company_id' => $this->sysCompanyId,
            'sys_model_id' => $idByClassName,
            'model_record_id' => $modelRecordId,
            'status_id' => $statusId,
        ];
        if ($typeId) {
            $condition['type_id'] = D3nTypeDictionary::getIdByType($idByClassName, $typeId);
        }

        return D3nNotification::findAll($condition);
    }
}
