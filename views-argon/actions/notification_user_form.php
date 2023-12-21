<?php

use cornernote\returnurl\ReturnUrl;use d3system\yii2\web\D3SystemView;
use d3yii2\d3notification\models\forms\UserFrom;
use eaArgonTheme\widget\ThButton;
use eaArgonTheme\widget\ThReturnButton;
use kartik\form\ActiveForm;
use yii\db\ActiveRecord;


/**
 * @var D3SystemView $this
 * @var ActiveRecord $model
 * @var UserFrom $formModel
 * @var string $formTitle
 * @var bool $formShowInputType
 * @var bool $formShowInputNotes
 * @var string $formSubmitButtonLabel
 */

$this->title = $formTitle;
$this->addPageButtons(ThReturnButton::widget([
        'backUrl' => ReturnUrl::getUrl()
]));

?>
<div class="row">
    <div class="col-md-9">
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <div class="form-body">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'NotificationForm',
                        'enableClientValidation' => true,
                        'errorSummaryCssClass' => 'error-summary alert alert-error',
                    ]);
                    echo $form->errorSummary($formModel);
                    if ($formShowInputType) {
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
                        echo $form
                            ->field($formModel, 'typeId')
                            ->dropDownList($formModel->typeListForDropdown());
                        echo $form
                            ->field($formModel, 'notes')
                            ->textarea([
                                'disabled' => true
                            ]);
                    }
                    if ($formShowInputNotes) {
                        echo $form
                            ->field($formModel, 'userNotes')
                            ->textarea();
                    }
                    ?>

                    <div class="form-footer">
                        <div class="pull-right">
                            <?= ThButton::widget([
                                'label' => $formSubmitButtonLabel,
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
