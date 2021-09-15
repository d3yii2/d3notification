<?php


namespace d3yii2\d3notification\logic;


use d3yii2\d3notification\models\D3nNotification;
use d3yii2\d3notification\models\D3nType;
use d3yii2\d3notification\models\D3nTypeUser;
use Yii;

class UserNotificationsLogic
{
    /**
     * @var int
     */
    private $userId;

    /**
     * DashboardLogic constructor.
     * @param $sys_company_id
     */
    public function __construct()
    {
        $this->userId = Yii::$app->user->id;
    }

    /**
     * @param int[] $statusIdList
     * @return array
     */
    public function getMyNotificationsList(): array
    {
        return D3nTypeUser::find()
            ->select([
                'd3n_notification.type_id',
                'cnt' => 'COUNT(*)',
                'd3n_notification.status_id',
                'minTime' => 'MIN(`time`)',
                'maxTime' => 'MAX(`time`)',
            ])
            ->joinWith('type')
            ->joinWith('type.d3nNotifications')
            ->where([
                'd3n_type_user.user_id' => $this->userId
            ])
            ->groupBy([
                'd3n_notification.type_id',
                'd3n_notification.status_id'
            ])
            ->asArray()
            ->all();
    }
}