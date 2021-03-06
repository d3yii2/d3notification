<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d3yii2\d3notification\models\base;

use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\models\D3nStatusHistoryQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the base-model class for table "d3n_status_history".
 *
 * @property integer $id
 * @property integer $notification_id
 * @property integer $status_id
 * @property string $time
 * @property integer $user_id
 *
 * @property \d3yii2\d3notification\models\D3nNotification $notification
 * @property \d3yii2\d3notification\models\D3nStatus $status
 * @property string $aliasModel
 */
abstract class D3nStatusHistory extends ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'd3n_status_history';
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            // 'attributeTypes' will be composed automatically according to `rules()`
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'smallint Unsigned' => [['status_id'],'integer' ,'min' => 0 ,'max' => 65535],
            'integer Unsigned' => [['id','notification_id','user_id'],'integer' ,'min' => 0 ,'max' => 4294967295],
            [['notification_id', 'status_id'], 'required'],
            [['time'], 'safe'],
            [['notification_id'], 'exist', 'skipOnError' => true, 'targetClass' => \d3yii2\d3notification\models\D3nNotification::className(), 'targetAttribute' => ['notification_id' => 'id']],
            //[['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => \d3yii2\d3notification\models\D3nStatus::className(), 'targetAttribute' => ['status_id' => 'id']]
            ['status_id', 'in', 'range' => array_keys(D3nStatusDictionary::getList())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('d3notification', 'ID'),
            'notification_id' => Yii::t('d3notification', 'Notification ID'),
            'status_id' => Yii::t('d3notification', 'Status ID'),
            'time' => Yii::t('d3notification', 'Time'),
            'user_id' => Yii::t('d3notification', 'User ID'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(\d3yii2\d3notification\models\D3nNotification::className(), ['id' => 'notification_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(\d3yii2\d3notification\models\D3nStatus::className(), ['id' => 'status_id']);
    }


    
    /**
     * @inheritdoc
     * @return D3nStatusHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new D3nStatusHistoryQuery(get_called_class());
    }


}
