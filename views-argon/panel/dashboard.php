<?php

use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\dictionaries\D3nTypeDictionary;
use eaArgonTheme\widget\ThTableSimple2;

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
            'header' => Yii::t('d3notification', 'From'),
            'attribute' => 'minTime',
        ],
        [
            'header' => Yii::t('d3notification', 'To'),
            'attribute' => 'maxTime',
        ],
    ],
    'data' => $data
]);
