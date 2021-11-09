<?php

namespace d3yii2\d3notification\controllers;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3notification\accessRights\D3NotesFullUserRole;
use d3yii2\d3notification\models\D3nNotification;
use d3yii2\d3notification\models\D3nNotificationSearch;
use d3yii2\d3notification\models\D3nStatusHistory;
use d3yii2\d3notification\models\D3nType;
use d3yii2\d3notification\models\D3nTypeUser;
use d3yii2\d3notification\Module;
use eaBlankonThema\yii2\web\LayoutController;
use Exception;
use thrieu\grid\ClearFilterStateBehavior;
use Throwable;
use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\filters\AccessControl;
use eaBlankonThema\components\FlashHelper;
use yii\web\Response;
use yii2d3\d3persons\models\User;

/**
 * NotificationController implements the CRUD actions for D3nNotification model.
 */
class NotificationController extends LayoutController
{
    /**
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
     * @var Module $module
     */
    public $enableCsrfValidation = false;

    /**
     * specify route for identifing active menu item
     */
    public $menuRoute = 'd3notification/notification/index';

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                            'view',
                            'change-status',
                            'delete',
                            'add-person',
                            'remove-person',
                            'persons',
                        ],
                        'roles' => [
                            D3NotesFullUserRole::NAME
                        ],
                    ],
                ],
            ],
            'clearFilterState' => ClearFilterStateBehavior::class,
        ];
    }


    public function actionPersons()
    {
        $types = D3nType::find()
            ->joinWith('d3nTypeUsers')
            ->joinWith('d3nTypeUsers.user')
            ->all();
        
        return $this->render('persons', compact('types'));
    }
    
    public function actionAddPerson(int $id)
    {
        $type = D3nType::findOne($id);
        
        $model = new D3nTypeUser();
        $model->type_id = $type->id;
        $model->alert_type = D3nTypeUser::ALERT_TYPE_EMAIL;

        //@FIXME TMP hacks - jāstaisa korekta ajax meklēšana formā
        $model->user_id = 4;
 
        try {
    
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        
                $user = User::findOne($model->user_id);
                
                if ($user->id === $model->user_id) {
                    throw new \yii\base\Exception('This User added already');
                }
        
                if (!$model->save()) {
                    throw new D3ActiveRecordException($model);
                }
                FlashHelper::addSuccess(Yii::t('d3notification', 'Person added to notification'));
                $this->redirect(Url::to(['persons']));
            }
        } catch (Exception $e) {
            FlashHelper::processException($e);
            Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
        
        return $this->render('add-person-form', compact('model'));
    }
    
    public function actionRemovePerson(int $id)
    {
        try {
            $user = D3nTypeUser::findOne($id);
    
            if ($user) {
                if (!$user->delete()) {
                    throw new \yii\db\Exception('Cannot delete notification person: ' . Json::encode($user->getErrors()));
                }
                FlashHelper::addSuccess(Yii::t('d3notification', 'Notification Person removed'));
            }
        } catch (\yii\base\Exception $e) {
            Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            FlashHelper::processException($e);
        }
        
        return $this->redirect(Url::to(['/d3notification/notification/persons']));
    }
    
    /**
     * Lists all D3nNotification models.
     * @return string
     * @throws \yii\db\Exception|\yii\base\InvalidConfigException
     */
    public function actionIndex(): string
    {
        $searchModel = new D3nNotificationSearch;
        $searchModel->status_id = $this->module->getActualStatusesIdList();
        $dataProvider = $searchModel->search();

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single D3nNotification model.
     * @param integer $id
     *
     * @return string
     * @throws HttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * @throws \yii\web\HttpException
     * @throws \yii\db\Exception
     */
    public function actionChangeStatus(int $id, int $status_id)
    {
        $model = $this->findModel($id);
        $model->status_id = $status_id;

        $notificationClass = SysModelsDictionary::getClassList()[$model->sys_model_id];
        /** @var \d3yii2\d3notification\interfaces\Notification $notificationModel */
        $notificationModel = new $notificationClass();
        $statusModel = new D3nStatusHistory();
        $statusModel->notification_id = $model->id;
        $statusModel->time = date('Y-m-d H:i:s');
        $statusModel->user_id = Yii::$app->user->id;
        $statusModel->status_id = $status_id;
        if (!$transaction = Yii::$app->getDb()->beginTransaction()) {
            throw new \yii\db\Exception('Can not initate transaction');
        }
        try {
            if (method_exists($notificationModel, 'isRequiredNotes')
                && $notificationModel->isRequiredNotes()
            ) {
                if (($post = $this->request->post())
                    && $statusModel->load($post)
                    && $statusModel->save()
                ) {
                    if (!$model->save()) {
                        throw new D3ActiveRecordException($model);
                    }
                    $transaction->commit();
                    return $this->redirect([
                        'view',
                        'id' => $id
                    ]);
                }

                return $this->render('change_status', [
                    'model' => $model,
                    'notificationModel' => $notificationModel,
                    'statusModel' => $statusModel
                ]);
            }
            if (!$statusModel->save()) {
                throw new D3ActiveRecordException($statusModel);
            }
            if (!$model->save()) {
                throw new D3ActiveRecordException($model);
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            FlashHelper::addDanger($e->getMessage());
            Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
        return $this->redirect([
            'view',
            'id' => $id
        ]);
    }

    /**
     * Deletes an existing D3nNotification model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return Response
     * @throws HttpException
     * @throws Throwable
     */
    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);
        if (!$transaction = Yii::$app->getDb()->beginTransaction()) {
            throw new \yii\db\Exception('Can not initate transaction');
        }
        try {
            $model->delete();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            FlashHelper::processException($e);
        }

        return $this->redirect(['index']);
    }

    /**
     * Update D3nNotification model record by editable.
     * @param integer $id
     * @return array|bool
     * @throws HttpException
     */
    public function actionEditable(int $id)
    {

        $request = Yii::$app->request;

        // Check if there is an Editable ajax request
        if (!$request->post('hasEditable')) {
            return false;
        }

        $post = [];
        foreach ($request->post() as $name => $value) {
            //if(in_array($name,$this->editAbleFileds)){
            $post[$name] = $value;
            //}
        }

        // use Yii's response format to encode output as JSON
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$post) {
            return ['output' => '', 'message' => Yii::t('crud', 'Cannot update this field')];
        }

        $model = $this->findModel($id);
        $model->setAttributes($post, true);
        // read your posted model attributes
        if ($model->save()) {
            // read or convert your posted information
            $value = $model->$name;

            // return JSON encoded output in the below format
            return ['output' => $value, 'message' => ''];

            // alternatively you can return a validation error
            // return ['output'=>'', 'message'=>Yii::t('crud', 'Validation error')];
        }
        // else if nothing to do always return an empty JSON encoded output

        //  return ['output'=>'', 'message'=>''];
        $errors = [];
        foreach ($model->errors as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = $model->getAttributeLabel($field)
                    . ': '
                    . $message;
            }
        }
        return ['output' => '', 'message' => implode('<br>', $errors)];
    }

    /**
     * Finds the D3nNotification model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return D3nNotification the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel(int $id): D3nNotification
    {
        if (($model = D3nNotification::findOne($id)) === null) {
            throw new HttpException(404, Yii::t('crud', 'The requested page does not exist.'));
        }
        return $model;
    }
}
