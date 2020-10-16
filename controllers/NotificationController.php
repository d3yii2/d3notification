<?php

namespace d3yii2\d3notification\controllers;

use d3yii2\d3notification\accessRights\D3NotesFullUserRole;
use d3yii2\d3notification\models\D3nNotification;
use d3yii2\d3notification\models\D3nNotificationSearch;
use d3yii2\d3notification\Module;
use ea\app\controllers\LayoutController;
use Exception;
use thrieu\grid\ClearFilterStateBehavior;
use Throwable;
use Yii;
use yii\web\HttpException;
use yii\filters\AccessControl;
use eaBlankonThema\components\FlashHelper;
use yii\web\Response;

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
                            'delete'
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


    /**
     * Lists all D3nNotification models.
     * @return mixed
     */
    public function actionIndex()
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


    public function actionChangeStatus(int $id, int $status_id): Response
    {

        $model = $this->findModel($id);
        $model->status_id = $status_id;
        $model->save();
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
    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        $transaction = Yii::$app->getDb()->beginTransaction();

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
