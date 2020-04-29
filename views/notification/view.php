<?php

use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\dictionaries\D3nTypeDictionary;
use d3yii2\d3notification\interfaces\Notification;
use eaBlankonThema\widget\ThAlertList;
use d3system\yii2\web\D3SystemView;
use eaBlankonThema\widget\ThButtonDropDown;
use eaBlankonThema\widget\ThExternalLink;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use kartik\editable\Editable;
use kartik\grid\GridView;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use eaBlankonThema\widget\ThReturnButton;

LayoutAsset::register($this);


/**
 * @var D3SystemView $this
 * @var d3yii2\d3notification\models\D3nNotification $model
 */
$copyParams = $model->attributes;

$this->title = Yii::t('d3notification', 'Notification');
$this->setPageHeader($this->title);
$this->setPageIcon('info');

$this->addPageButtons(ThReturnButton::widget(['backUrl' => ['index']]));


$notificationClass = SysModelsDictionary::getClassList()[$model->sys_model_id];
/** @var Notification $notificationModel */
$notificationModel = new $notificationClass();
$dropDownItems = [];
foreach ($notificationModel->getNotificationStatusList() as $statusId => $statusNem) {
    if($statusId === $model->status_id){
        continue;
    }
    $notificationModel->statusId = $statusId;
    $realStatusId = D3nStatusDictionary::getIdByNotificationStatus($model->sys_model_id,$notificationModel);
    $dropDownItems[] = [
        'label' => $statusNem,
        'url' => [
            'change-status',
            'id' => $model->id,
            'status_id' => $realStatusId
        ],
    ];
}

$this->addPageButtons(ThButtonDropDown::widget([
    'label' => Yii::t('crud', 'Change Status'),
    'items' => $dropDownItems,
    'type' => ThButton::TYPE_PRIMARY
]));


$this->beginBlock('d3yii2\d3notification\models\D3nNotification');

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'time',
            'label' => Yii::t('d3notification', 'Time'),
        ],
        [
            'attribute' => 'sys_model_id',
            'label' => Yii::t('d3notification', 'Table'),
            'value' => SysModelsDictionary::getList()[$model->sys_model_id] ?? '???'
        ],
        [
            'attribute' => 'type_id',
            'label' => Yii::t('d3notification', 'Type'),
            'value' => D3nTypeDictionary::getList()[$model->type_id],

        ],
        [
            'attribute' => 'status_id',
            'header' => Yii::t('d3notification', 'Status'),
            'value' => D3nStatusDictionary::getList()[$model->status_id] ?? '???'
        ],

        [
            'attribute' => 'model_record_id',
        ],
        [
            'attribute' => 'key',
        ],
    ],
]);
$attributes = [];
foreach(Json::decode($model->data) as $name => $value){
    $attributes[] = [
            'label' => $name,
        'value' => $value
    ];
}
/** @var Notification $notificationModel */
$notificationModel = $model->getNotificationModel();
foreach($notificationModel->getLinkList() as $link){
    $attributes[] = [
       'label' => $link['label'],
        'value' => ThExternalLink::widget([
            'url' => $link['url'],
            'text' => $link['value']
        ])
    ];
}
echo DetailView::widget([
    'model' => $model,
    'attributes' => $attributes
]);

?>


<hr/>

<?= ThButton::widget([
    'label' => Yii::t('crud', 'Delete'),
    'link' => ['delete', 'id' => $model->id],
    'icon' => ThButton::ICON_TRASH,
    'type' => ThButton::TYPE_DANGER,
    'htmlOptions' => [
        'data-confirm' => '' . Yii::t('crud', 'Are you sure to delete this item?') . '',
        'data-method' => 'post',
    ]
]) ?>
<?php $this->endBlock(); ?>


<?php $this->beginBlock('D3nStatusHistories'); ?>

<div class="panel rounded shadow">
    <div class="panel-heading">
        <div class="pull-left">
            <h3 class="panel-title">
                <?= Yii::t('crud', 'Status History') ?>
            </h3>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body no-padding">
        <?php Pjax::begin(['id' => 'pjax-D3nStatusHistories', 'enableReplaceState' => false, 'linkSelector' => '#pjax-D3nStatusHistories ul.pagination a, th a', 'clientOptions' => ['pjax:success' => 'function(){alert("yo")}']]) ?>
        <div class="table-responsive">
            <?php
            echo GridView::widget([
                'layout' => '{items}{pager}',
                'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->getD3nStatusHistories(), 'pagination' => ['pageSize' => 20, 'pageParam' => 'page-d3nstatushistories']]),
                'export' => false,
                'tableOptions' => [
                    'class' => 'table table-striped table-success'
                ],

                //    'pager'        => [
                //        'class'          => yii\widgets\LinkPager::class,
                //        'firstPageLabel' => Yii::t('crud', 'First'),
                //        'lastPageLabel'  => Yii::t('crud', 'Last')
                //    ],
                'columns' => [
                    [
                        'class' => '\kartik\grid\EditableColumn',
                        'attribute' => 'status_id',
                        'editableOptions' => [
                            'formOptions' => [
                                'action' => [
                                    'd3n-status-history/editable-column-update'
                                ]
                            ],
                            'inputType' => Editable::INPUT_TEXT,

                        ]
                    ],

                    [
                        'class' => '\kartik\grid\EditableColumn',
                        'attribute' => 'time',
                        'editableOptions' => [
                            'formOptions' => [
                                'action' => [
                                    'd3n-status-history/editable-column-update'
                                ]
                            ],
                            'inputType' => Editable::INPUT_TEXT,


                        ]
                    ],

                    [
                        'class' => '\kartik\grid\EditableColumn',
                        'attribute' => 'user_id',
                        'editableOptions' => [
                            'formOptions' => [
                                'action' => [
                                    'd3n-status-history/editable-column-update'
                                ]
                            ],
                            'inputType' => Editable::INPUT_TEXT,
                        ]
                    ],

                    [
                        'class' => kartik\grid\ActionColumn::class,
                        'template' => '{view} {update} {delete}',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            $params = is_array($key) ? $key : ['id' => (string)$key];
                            $params[0] = 'd3n-status-history/' . $action;
                            $params['D3nStatusHistory'] = ['notification_id' => $model->primaryKey()[0]];
                            return Url::toRoute($params);
                        },
                    ]
                ]
            ]);
            ?>
        </div>
        <?php Pjax::end() ?>

    </div>
</div>
<?php
$this->endBlock()
?>


<div class="row">
    <?= ThAlertList::widget() ?>
    <div class="col-md-4">
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom d3n-notification">
                <?= $this->blocks['d3yii2\d3notification\models\D3nNotification'] ?>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <?= $this->blocks['D3nStatusHistories'] ?>
    </div>
</div>
