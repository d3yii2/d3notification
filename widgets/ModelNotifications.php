<?php

namespace d3yii2\d3notification\widgets;

use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\dictionaries\D3nTypeDictionary;
use d3yii2\d3notification\models\D3nNotification;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThExternalLink;
use eaBlankonThema\widget\ThTableSimple2;
use Yii;
use yii\base\Widget;

class ModelNotifications extends Widget
{
    public $modelClass;
    public $modelRecordId;

    /**
     * @throws \d3system\exceptions\D3ActiveRecordException
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $statusList = D3nStatusDictionary::getList();
        $typeList = D3nTypeDictionary::getList();

        return ThTableSimple2::widget([
            'tableOptions' => [
                'class' => 'table table-striped table-success'
            ],

            'leftIcon' => 'bolt',
            'title' => ThButton::widget([
                    'tooltip' => Yii::t('manufacture', 'Add new notification'),
                    'link' => ['notification-create', 'id' => $this->modelRecordId],
                    'icon' => ThButton::ICON_PLUS,
                    'type' => ThButton::TYPE_PRIMARY
                ])
                . Yii::t('d3notification', 'Notifications'),
            'columns' => [
                [
                    'header' => Yii::t('d3notification', 'Time'),
                    'value' => static function (D3nNotification $model) {
                        return ThExternalLink::widget([
                            'text' => Yii::$app->formatter->asDatetime($model->time, 'short'),
                            'url' => ['/d3notification/notification/view', 'id' => $model->id]
                        ]);
                    },
                ],
                [
                    'header' => Yii::t('d3notification', 'Type'),
                    'value' => static function (D3nNotification $model) use ($typeList) {
                        return $typeList[$model->type_id] ?? $model->type_id;
                    },
                ],
                [
                    'header' => Yii::t('d3notification', 'Status'),
                    'value' => static function (D3nNotification $model) use ($statusList) {
                        return $statusList[$model->status_id] ?? $model->status_id;
                    },
                ],
                [
                    'header' => Yii::t('d3notification', 'Notes'),
                    'attribute' => 'notes',
                ],
            ],
            'data' => $this->getModelNotifications()
        ]);
    }

    /**
     * @return D3nNotification[]
     * @throws \d3system\exceptions\D3ActiveRecordException
     * @throws \yii\base\InvalidConfigException
     */
    public function getModelNotifications(): array
    {
        return D3nNotification::find()
            ->innerJoin('d3n_type', 'd3n_type.id = d3n_notification.type_id')
            ->where([
                'd3n_notification.sys_model_id' => SysModelsDictionary::getIdByClassName($this->modelClass),
                'd3n_notification.model_record_id' => $this->modelRecordId
            ])
            ->all();
    }
}
