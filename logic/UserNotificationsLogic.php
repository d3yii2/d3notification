<?php

namespace d3yii2\d3notification\logic;

use d3yii2\d3notification\models\D3nNotification;
use d3yii2\d3notification\models\D3nType;
use d3yii2\d3notification\models\D3nTypeUser;
use Yii;
use yii\helpers\ArrayHelper;

class UserNotificationsLogic
{
    public $statusId;
    public $typeId;
    
    /**
     * @var int
     */
    private $userId;
    
    /**
     * UserNotificationsLogic constructor.
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
        $sql = D3nTypeUser::find()
            ->select([
                'd3n_notification.*',
             //   'd3n_status.label',
             //   'd3n_notification.status_id',
            ])
            ->joinWith('type')
            ->joinWith('type.d3nNotifications.status')
            ->joinWith('type.d3nNotifications')
            ->where([
                'd3n_type_user.user_id' => $this->userId,
            ]);
        
        if ($this->statusId) {
            $sql->andWhere(['in', 'd3n_notification.status_id', $this->statusId]);
        }
        
        if ($this->typeId) {
            $sql->andWhere(['d3n_type_user.type_id' => $this->typeId]);
        }
        
        $data = $sql->asArray()->all();
        return $data;
    }
}