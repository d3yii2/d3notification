<?php

use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\dictionaries\D3nTypeDictionary;
use d3yii2\d3notification\models\D3nTypeUser;
use eaBlankonThema\widget\ThDataListColumn;
use eaBlankonThema\widget\ThTableSimple2;
use yii\helpers\Html;

$statusList = D3nStatusDictionary::getList();
$typeList = D3nTypeDictionary::getList();
/** @var array $data */
echo ThTableSimple2::widget([
    'tableOptions' => [
        'class' => 'table table-striped table-success'
    ],
    'title' => Html::a(Yii::t('d3notification', 'Notifications'), ['/d3notification/notification']),
    'columns' => [
        [
            'header' => Yii::t('d3notification', 'Time'),
            'value' => static function (array $row) {
                $datetime = new DateTime($row['time']);
                return $datetime->format('d.m.y');
            },
        ],
        [
            'header' => Yii::t('d3notification', 'Type'),
            'value' => static function (array $row) {
                return Html::a($row['type']['label'], ['/d3notification/notification/view', 'id' => $row['id']]);
            },
        ],
        [
            'header' => Yii::t('d3notification', 'Status'),
            'value' => static function (array $row) use ($statusList) {
                return $statusList[$row['status_id']] ? Yii::t('d3notification', $statusList[$row['status_id']]) : $row['status_id'];
            },
        ],
    ],
    'data' => $data
]);