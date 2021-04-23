<?php

use humhub\modules\xcoin\models\Config;
use humhub\widgets\Button;
use yii\bootstrap\ActiveForm;

/* @var $model Config */
?>

<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('XcoinModule.base', '<strong>Xcoin</strong> module configuration'); ?></div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'configure-form']); ?>

            <?= $form->field($model, 'isCrowdfundingEnabled')->checkbox(); ?>

            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> <?= Yii::t('XcoinModule.base', 'Leave fields blank in order to disable a restriction.') ?>
            </div>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
