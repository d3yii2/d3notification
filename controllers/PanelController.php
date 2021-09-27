<?php
namespace d3yii2\d3notification\controllers;

use d3yii2\d3notification\accessRights\D3NotesFullUserRole;
use d3yii2\d3notification\logic\DashboardLogic;
use d3yii2\d3notification\logic\UserNotificationsLogic;
use unyii2\yii2panel\Controller;
use Yii;
use yii\filters\AccessControl;

/**
 * @property \d3yii2\d3notification\Module $module
 */
class PanelController extends Controller
{
    /**
     * @var mixed
     */
    public $statusIdList;

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
                            'my-notifications',
                        ],
                        'roles' => [
                            D3NotesFullUserRole::NAME,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws \yii\db\Exception|\yii\base\InvalidConfigException
     */
    public function actionDashboard(array $statusIdList): string
    {
        $logic = new DashboardLogic(Yii::$app->SysCmp->getActiveCompanyId());
        return $this->render('dashboard', [
            'data' => $logic->getList($statusIdList)
        ]);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function actionMyNotifications(array $statusIdList): string
    {
        $logic = new UserNotificationsLogic(Yii::$app->user->id);
        $logic->statusId = $statusIdList;
        return $this->render('my-notifications', [
            'data' => $logic->getMyNotificationsList()
        ]);
    }
}
