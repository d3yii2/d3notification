<?php

use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\dictionaries\D3nTypeDictionary;
use d3yii2\d3notification\models\D3nTypeUser;
use eaBlankonThema\widget\ThDataListColumn;
use eaBlankonThema\widget\ThTableSimple2;

$statusList = D3nStatusDictionary::getList();
$typeList = D3nTypeDictionary::getList();
/** @var array $data */
echo ThTableSimple2::widget([
    'tableOptions' => [
        'class' => 'table table-striped table-success'
    ],
    'title' => Yii::t('d3notification', 'Notifications'),
    'columns' => [
        [
            'header' => Yii::t('d3notification', 'Type'),
            'value' => static function (array $row) use ($typeList) {
                return $typeList[$row['type_id']] ?? $row['type_id'];
            },
        ],
        [
            'header' => Yii::t('d3notification', 'Count'),
            'attribute' => 'cnt',
        ],
        [
            'header' => Yii::t('d3notification', 'Status'),
            'value' => static function (array $row) use ($statusList) {
                return $statusList[$row['status_id']] ?? $row['status_id'];
            },
        ],
        [
            'attribute' => 'alert_type',
            'header' => Yii::t('d3notification', 'Alert Type'),
            'value' => static function (array $row) use ($statusList) {
                return D3nTypeUser::getAlertTypeValueLabel($row['alert_type']);
            },
        ],
    
    ],
    'data' => $data
]);