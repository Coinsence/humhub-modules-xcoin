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

        <h4>
            <?= Yii::t('XcoinModule.base', 'Coin transfer settings'); ?>
        </h4>
        <?= $form->field($model, 'allowDirectCoinTransfer')->checkbox([
                'label' => Yii::t('XcoinModule.base', 'Allow direct coin transfer'),
                'uncheck' => 0,
                'checked' => 1,
        ]) ?>

        <?= Html::submitButton('Save', ['class' => 'btn btn-default', 'data-ui-loader' => '']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>