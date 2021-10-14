<?php

use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\dictionaries\D3nTypeDictionary;
use eaBlankonThema\widget\ThExternalLink;
use eaBlankonThema\widget\ThTableSimple2;
use yii\helpers\Html;

$statusList = D3nStatusDictionary::getList();
$typeList = D3nTypeDictionary::getList();
/** @var array $data */
?>
<div class="panel  rounded shadow col-sm-10 col-md-14 col-lg-10">
<?=ThTableSimple2::widget([
    'tableOptions' => [
        'class' => 'table table-striped table-success'
    ],
    'leftIcon' => 'bolt',
    'title' => Html::a(Yii::t('d3notification', 'Notifications'), ['/d3notification/notification']),
    'columns' => [
        [
            'header' => Yii::t('d3notification', 'Time'),
            'value' => static function (array $row) {
                return ThExternalLink::widget([
                    'text' => Yii::$app->formatter->asDatetime($row['time'], 'short'),
                    'url' => ['/d3notification/notification/view', 'id' => $row['id']]
                ]);
            },
        ],
        [
            'header' => Yii::t('d3notification', 'Type'),
            'attribute' => 'typeLabel',
        ],
        [
            'header' => Yii::t('d3notification', 'Status'),
            'value' => static function (array $row) use ($statusList) {
                return $statusList[$row['status_id']] ? Yii::t('d3notification', $statusList[$row['status_id']]) : $row['status_id'];
            },
        ],
        [
            'header' => Yii::t('d3notification', 'Notes'),
            'attribute' => 'notes',
        ],
    ],
    'data' => $data
])?>
</div>
