<?php

use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\dictionaries\D3nTypeDictionary;
use eaArgonTheme\widget\ThDataListColumn;
use eaArgonTheme\widget\ThDateColumn;
use yii\helpers\Html;
use yii\widgets\Pjax;
use eaArgonTheme\widget\ThRmGridView;
use thrieu\grid\FilterStateBehavior;
use d3system\yii2\web\D3SystemView;


/**
 * @var D3SystemView $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var d3yii2\d3notification\models\D3nNotificationSearch $searchModel
 */


$this->title = Yii::t('d3notification', 'Notifications');
$this->setPageHeader($this->title);
//$this->setPageIcon('info');
?>

<div class="row">
    <div class="col-md-12">
        <?php
        echo Html::beginForm('', 'post', ['id' => 'form-remember']);
        echo Html::hiddenInput('clear-state', '1');
        echo Html::hiddenInput('redirect-to', '');
        echo Html::endForm();

        Pjax::begin([
            'id' => 'pjax-grid',
            'enableReplaceState' => false,
            'linkSelector' => '#pjax-main ul.pagination a, th a',
            'clientOptions' => [
                'pjax:success' => 'function(){alert("yo")}'
            ],
            'timeout' => false,
            'enablePushState' => false
        ]);

        echo ThRmGridView::widget([
            'as filterBehavior' => FilterStateBehavior::class,
            'dataProvider' => $dataProvider,
            'actionColumnTemplate' => '{view}',
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => ThDateColumn::class,
                    'attribute' => 'time_local',
                    'header' => Yii::t('d3notification', 'Time'),
                ],
//                [
//                    'class' => ThDataListColumn::class,
//                    'attribute' => 'sys_model_id',
//                    'header' => Yii::t('d3notification','Table'),
//                    'list' => SysModelsDictionary::getLabelList()
//                ],
                [
                    'attribute' => 'type_id',
                    'class' => ThDataListColumn::class,
                    'header' => Yii::t('d3notification', 'Type'),
                    'list' => D3nTypeDictionary::getList()
                ],
                [
                    'attribute' => 'status_id',
                    'class' => ThDataListColumn::class,
                    'header' => Yii::t('d3notification', 'Status'),
                    'list' => D3nStatusDictionary::getList()
                ],
                [
                    'attribute' => 'data',
                    'format' => 'raw'
                 ],
            ],
        ]);
        Pjax::end()
        ?>
    </div>
</div>
