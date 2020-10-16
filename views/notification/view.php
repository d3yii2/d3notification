<?php

use d3yii2\d3notification\models\D3nStatusHistory;
use eaBlankonThema\widget\ThDataListColumn;
use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use d3yii2\d3notification\dictionaries\D3nTypeDictionary;
use d3yii2\d3notification\interfaces\Notification;
use eaBlankonThema\widget\ThAlertList;
use d3system\yii2\web\D3SystemView;
use eaBlankonThema\widget\ThButtonDropDown;
use eaBlankonThema\widget\ThExternalLink;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use eaBlankonThema\widget\ThReturnButton;
use yii2d3\d3persons\models\User;
Use eaBlankonThema\assetbundles\widgets\ThGridViewAsset;

LayoutAsset::register($this);
ThGridViewAsset::register($this);


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
            'value' => SysModelsDictionary::getLabelList()[$model->sys_model_id] ?? '???'
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
        'format' => 'raw',
        'value' => $value
    ];
}
/** @var Notification $notificationModel */
$notificationModel = $model->getNotificationModel();
foreach($notificationModel->getNotificationLinkList() as $link){
    $attributes[] = [
        'label' => $link['label'],
        'format' => 'raw',
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
        <div class="table-responsive">
            <?php
            echo GridView::widget([
                'layout' => '{items}',
                'dataProvider' => new ActiveDataProvider([
                    'query' => $model->getD3nStatusHistories(),
                    'pagination' => false
                ]),
                'export' => false,
                'tableOptions' => [
                    'class' => 'table table-striped table-success'
                ],
                'columns' => [
                    [
                        'class' => ThDataListColumn::class,
                        'header' => 'Status',
                        'attribute' => 'status_id',
                        'list' => D3nStatusDictionary::getList()
                    ],

                    [
                        'attribute' => 'time',
                    ],

                    [
                        'attribute' => 'user_id',
                        'header' => 'User',
                        'value' => static function(D3nStatusHistory $model){
                            if(!$user = User::findOne($model->user_id)){
                                return $model->user_id;
                            }
                            return $user->username;
                        }

                    ],

                ]
            ]);
            ?>
        </div>
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
