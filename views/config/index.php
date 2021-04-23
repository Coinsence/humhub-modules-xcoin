<?php
/**
 * Created by Safouane Fakhfakh.
 * Email: Safouane.Fakhfakh@mail.com
 *
 * @var $this yii\web\View
 * @var $model \humhub\modules\xcoin\models\SpaceModuleBasicSettings
 */

use yii\widgets\ActiveForm;
use \yii\helpers\Html;
?>

<div class="panel panel-default">

    <?php $this->beginContent('@xcoin/views/config/_moduleConfigLayout.php', ['space' => $space]) ?>

        <?php $form = ActiveForm::begin(); ?>

            <h4>
                <?= Yii::t('XcoinModule.config', 'Space coins allocation transaction parameters'); ?>
            </h4>

            <div class="help-block">
                <?= Yii::t('XcoinModule.config', 'When a member joins a space coins allocation transaction is created for his account.') ?>
            </div>

            <?= $form->field($model, 'accountTitle')->input('string'); ?>

            <?= $form->field($model, 'transactionAmount')->input('number', ['min' => 1]); ?>

            <?= $form->field($model, 'transactionComment')->input('string') ?>

            <hr>

            <h4>
                <?= Yii::t('XcoinModule.config', 'Coin transfer settings'); ?>
            </h4>
            <?= $form->field($model, 'allowDirectCoinTransfer')->checkbox([
                'label' => Yii::t('XcoinModule.config', 'Allow direct coin transfer'),
                'uncheck' => 0,
                'checked' => 1,
            ]) ?>

            <?= Html::submitButton('Save', ['class' => 'btn btn-default', 'data-ui-loader' => '']) ?>
        <?php ActiveForm::end(); ?>

    <?php $this->endContent(); ?>

</div>