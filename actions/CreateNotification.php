<?php

namespace d3yii2\d3notification\actions;


use d3system\exceptions\D3UserAlertException;
use d3yii2\d3notification\interfaces\Notification;
use d3yii2\d3notification\logic\NotificationLogic;
use d3yii2\d3notification\models\forms\UserFrom;
use eaBlankonThema\components\FlashHelper;
use Yii;
use yii\base\Action;
use yii\db\Exception;

class CreateNotification extends Action
{
    /** @var string */
    public $notificationModelClass;

    /** @var int */
    public $typeId;

    /** @var string[]  */
    public $notesList = [];

    /** @var array */
    public $backUrl;

    /**
     * @throws \yii\db\Exception
     */
    public function run(int $id)
    {
        /** @var \yii\db\ActiveRecord $model  validate access rights*/
        $model = $this->controller->findModel($id);
        $formModel = new UserFrom();

        /** @var Notification|\yii\db\ActiveRecord $notification */
        $notification = new $this->notificationModelClass;
        $notification->attributes = $model->attributes;
        $notification->typeId = $this->typeId;
        $notification->statusId = $notification->getNotificationStatusNewId();
        $formModel->typeList = $this->notesList;

        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            $logic = new NotificationLogic(Yii::$app->SysCmp->getActiveCompanyId(), Yii::$app->user->id);
            if (!$transaction = Yii::$app->getDb()->beginTransaction()) {
                throw new Exception('Can not initiate transaction');
            }
            try {
                $logic->register($notification, $formModel->notes, $formModel->userNotes);
                $transaction->commit();
                return $this->controller->redirect($this->createBackUrl($id));
            } catch (D3UserAlertException $e) {
                $transaction->rollBack();
                FlashHelper::addDanger($e->getMessage());
            } catch (\Exception $e) {
                FlashHelper::processException($e);
                $transaction->rollBack();
            }
        }
        return $this->controller->render('@vendor/d3yii2/d3notification/views/actions/notification_user_form', [
            'model' => $model,
            'formModel' => $formModel,
            'backUrl' => $this->backUrl
        ]);
    }

    public function createBackUrl(int $id): array
    {
        $url = $this->backUrl;
        foreach ($url as $k => $v) {
            if ($v === '@id') {
                $url[$k] =  $id;
            }
        }
        return $url;
    }
}
