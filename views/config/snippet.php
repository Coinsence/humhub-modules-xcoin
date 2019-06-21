<?php
/**
 * Created by Safouane Fakhfakh.
 * Email: Safouane.Fakhfakh@mail.com
 *
 * @var $this yii\web\View
 * @var $model \humhub\modules\xcoin\models\SpaceModuleSettings
 */

use yii\widgets\ActiveForm;
use \yii\helpers\Html;
?>

<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('XcoinModule.base', '<strong>Xcoin</strong> module configuration'); ?></div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>

        <h4>
            <?= Yii::t('XcoinModule.base', 'Space join transaction parameters'); ?>
        </h4>

        <div class="help-block">
            <?= Yii::t('XcoinModule.base', 'When a member joins a space coins allocation transaction is created for his account.') ?>
        </div>

        <?= $form->field($model, 'accountTitle')->input('string'); ?>

        <?= $form->field($model, 'transactionAmount')->input('number', ['min' => 1]); ?>

        <?= $form->field($model, 'transactionComment')->input('string') ?>

        <hr>

        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'data-ui-loader' => '']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>