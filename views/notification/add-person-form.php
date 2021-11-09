<?php

use d3yii2\d3notification\models\D3nTypeUser;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThSelect2Autocomplete;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \d3yii2\d3notification\models\D3nTypeUser $model
 */

$this->title = Yii::t('d3notification', 'Assign User');
?>

<div class="panel rounded shadow">
    <div class="panel-heading">
        <div class="pull-left">
            <h3 class="panel-title">
            </h3>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <?php
            $form = ActiveForm::begin([
                'id' => 'D4OfferClient',
                'enableClientValidation' => true,
                'errorSummaryCssClass' => 'error-summary alert alert-error',
            ]); ?>

        <?= $form->field(
            $model,
            'user_id'
        )->widget(
            ThSelect2Autocomplete::class,
            [
                'model' => $model,
                'attribute' => 'user_id',
                'url' => Url::to(['/d3persons/d3p-person/person-email-search']),
                //'data' => $model->customer_company_id ? [$model->customer_company_id => $model->customerCompany->name] : [],
                'options' => [
                    'prompt' => Yii::t('crud', 'Select'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                'inputMinLength' => 2
            ]
        ) ?>
        
        <?= $form->field($model, 'alert_type')->radioList(D3nTypeUser::optsAlertType()) ?>
        
        <?= Html::submitButton(Yii::t('blankonthema', 'Add'), ['class' => 'btn btn-primary']) ?>
        
        <?php
         ActiveForm::end();
        ?>
    </div>
</div>
