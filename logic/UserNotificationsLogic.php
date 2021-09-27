<?php

namespace d3yii2\d3notification\logic;

use d3yii2\d3notification\models\D3nNotification;

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
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getMyNotificationsList(): array
    {
        $sql = D3nNotification::find()
            ->select([
                'd3n_notification.*',
                'typeLabel' => 'd3n_type.label'
            ])
            ->innerJoin('d3n_type_user', 'd3n_type_user.type_id = d3n_notification.type_id')
            ->innerJoin('d3n_type', 'd3n_type.id = d3n_notification.type_id')
            ->where([
                'd3n_type_user.user_id' => $this->userId,
            ])
            ->limit(10)
        ;
        
        if ($this->statusId) {
            $sql->andWhere(['d3n_notification.status_id' => $this->statusId]);
        }
        
        if ($this->typeId) {
            $sql->andWhere(['d3n_notification.type_id' => $this->typeId]);
        }
        
        return $sql->asArray()->all();
    }
}
