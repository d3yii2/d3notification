<?php

namespace d3yii2\d3notification\actions;

use cornernote\returnurl\ReturnUrl;
use d3system\helpers\FlashHelper;
use d3yii2\d3notification\interfaces\Notification;
use d3yii2\d3notification\logic\NotificationLogic;
use d3yii2\d3notification\models\forms\UserFrom;
use Yii;
use yii\base\Action;
use yii\base\UserException;
use yii\db\ActiveRecord;
use yii\db\Exception;

class CreateNotification extends Action
{
    public ?string $notificationModelClass = null;

    public ?int $typeId = null;

    /** @var string[]  */
    public array $notesList = [];

    /** @var array */
    public ?array $backUrl = null;

    public string $viewPath = '@vendor/d3yii2/d3notification/views/actions/notification_user_form';
    public ?string $formModelClassName = null;
    public ?string $formTitle = null;
    public ?string $formSubmitButtonLabel =null;

    public function init(): void
    {
        parent::init();
        if (!$this->formTitle) {
            $this->formTitle = Yii::t('d3notification', 'Create Notification');
        }
        if (!$this->formSubmitButtonLabel) {
            $this->formSubmitButtonLabel = Yii::t('crud', 'Add');
        }
        if (!$this->formModelClassName) {
            $this->formModelClassName = UserFrom::class;
        }
    }

    /**
     * @throws Exception
     */
    public function run(int $id)
    {
        /** @var ActiveRecord $model  validate access rights*/
        $model = $this->controller->findModel($id);
        $formModel = new $this->formModelClassName;


        /** @var Notification|ActiveRecord $notification */
        $notification = new $this->notificationModelClass;
        $notification->setAttributes($model->attributes);
        $notification->typeId = $this->typeId;
        $notification->statusId = $notification->getNotificationStatusNewId();
        if (property_exists($formModel, 'typeList')) {
            $formModel->typeList = $this->notesList;
        }

        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            $logic = new NotificationLogic(Yii::$app->SysCmp->getActiveCompanyId(), Yii::$app->user->id);
            if (!$transaction = Yii::$app->getDb()->beginTransaction()) {
                throw new Exception('Can not initiate transaction');
            }
            try {
                $notes = property_exists($formModel, 'notes')?$formModel->notes:null;
                $userNotes = property_exists($formModel, 'userNotes')?$formModel->userNotes:null;
                $logic->register($notification, $notes, $userNotes);
                $transaction->commit();
                return $this->controller->redirect(ReturnUrl::getUrl());
            } catch (UserException $e) {
                $transaction->rollBack();
                FlashHelper::addDanger($e->getMessage());
            } catch (\Exception $e) {
                FlashHelper::processException($e);
                $transaction->rollBack();
            }
        }
        return $this->controller->render($this->viewPath, [
            'model' => $model,
            'formModel' => $formModel,
            'backUrl' => $this->backUrl,
            'formTitle' => $this->formTitle,
            'formSubmitButtonLabel' => $this->formSubmitButtonLabel
        ]);
    }
}
