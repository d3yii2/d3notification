<?php

use d3system\yii2\web\D3SystemView;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use eaBlankonThema\widget\ThAlertList;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThReturnButton;
use kartik\form\ActiveForm;

LayoutAsset::register($this);

/**
 * @var D3SystemView $this
 * @var \yii\db\ActiveRecord $model
 * @var \d3yii2\d3notification\models\forms\UserFrom $formModel
 */
$this->registerJs("
 $('#userfrom-typeid').change(function(){
    if($('#userfrom-typeid').val() === '" . $formModel::OTHER . "') { 
        $('#userfrom-notes').removeAttr('disabled');
    } else {
        $('#userfrom-notes').val('');
        $('#userfrom-notes').prop('disabled', true );
    }
})
");
$this->title = Yii::t('d3notification', 'Create Notification');
$this->setPageHeader($this->title);
$this->setPageIcon('info');
$this->addPageButtons(ThReturnButton::widget(['backUrl' => ['view', 'id' => $model->id]]));

?>
<div class="row">
    <?= ThAlertList::widget() ?>
    <div class="col-md-9">
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <div class="form-body">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'NotificationForm',
                        'enableClientValidation' => true,
                        'errorSummaryCssClass' => 'error-summary alert alert-error',
                    ]); ?>

                    <?= $form
                        ->field($formModel, 'typeId')
                        ->dropDownList($formModel->typeListForDropdown())
                    ?>
                    <?= $form
                        ->field($formModel, 'notes')
                        ->textarea([
                            'disabled' => true
                        ])?>
                    <?= $form
                        ->field($formModel, 'userNotes')
                        ->textarea()?>

                    <div class="form-footer">
                        <div class="pull-right">
                            <?= ThButton::widget([
                                'label' => Yii::t('crud', 'Create'),
                                'id' => 'save-form',
                                'icon' => ThButton::ICON_CHECK,
                                'type' => ThButton::TYPE_SUCCESS,
                                'submit' => true,
                                'htmlOptions' => [
                                    'name' => 'action',
                                    'value' => 'save',
                                ],
                            ]) ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
