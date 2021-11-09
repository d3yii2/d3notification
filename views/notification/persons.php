<?php

use eaBlankonThema\widget\ThAlertList;
use eaBlankonThema\widget\ThButton;
use yii\helpers\Url;

$this->title = Yii::t('d3notification', 'Notifications Users');
?>
<?= ThAlertList::widget() ?>

<div class="panel rounded shadow">
    <div class="panel-heading">
        <div class="pull-left">
            <h3 class="panel-title">
            </h3>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body no-padding">
        <table class="table table-bordered">
            <?php foreach ($types as $type): ?>
                <tr>
                    <td style="width:20%"><?= $type->label ?></td>
                    <td>
                        <?= ThButton::widget([
                            'icon' => ThButton::ICON_PLUS,
                            'size' => ThButton::SIZE_SMALL,
                            'type' => ThButton::TYPE_SUCCESS,
                            'link' => Url::to([
                                '/d3notification/notification/add-person',
                                'id' => $type->id
                            ])
                        ]) ?>
                        <?php
                        $typeUsers = $type->d3nTypeUsers;
                        if ($typeUsers): ?>
                            <table class="table">
                                <thead>
                                    <th>User</th>
                                    <th>Alert Type</th>
                                </thead>
                                <?php
                                foreach ($typeUsers as $typeUser): ?>
                                    <tr>
                                        <td><?= $typeUser->user->username ?></td>
                                        <td><?= $typeUser->alert_type ?></td>
                                        <td><?= ThButton::widget([
                                                'icon' => ThButton::ICON_TRASH,
                                                'size' => ThButton::SIZE_SMALL,
                                                'type' => ThButton::TYPE_DANGER,
                                                'link' => Url::to(['/d3notification/notification/remove-person', 'id' => $typeUser->id
                                            ])]) ?></td>
                                    </tr>
                                <?php
                                endforeach; ?>
                            </table>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
