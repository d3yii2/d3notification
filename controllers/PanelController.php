<?php
namespace d3yii2\d3notification\controllers;

use d3yii2\d3notification\accessRights\D3NotesFullUserRole;
use d3yii2\d3notification\logic\DashboardLogic;
use d3yii2\d3notification\logic\UserNotificationsLogic;
use unyii2\yii2panel\Controller;
use Yii;
use yii\filters\AccessControl;

class PanelController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'dashboard',
                            'my-notifications'
                        ],
                        'roles' => [
                            D3NotesFullUserRole::NAME,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function actionDashboard(array $statusIdList): string
    {
        $logic = new DashboardLogic(Yii::$app->SysCmp->getActiveCompanyId());
        return $this->render('dashboard',[
            'data' => $logic->getList($statusIdList)
        ]);
    }

    public function actionMyNotifications(): string
    {
        $logic = new UserNotificationsLogic();
        return $this->render('my-notifications',[
            'data' => $logic->getMyNotificationsList()
        ]);
    }

}