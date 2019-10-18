<?php
/**
 * Created by Mortadha Ghanmi.
 * Email: mortadha.ghanmi56@gmail.com
 *
 * @var $this yii\web\View
 * @var $model \humhub\modules\xcoin\models\SpaceModuleScheduleSettings
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="panel panel-default">

    <?php $this->beginContent('@xcoin/views/config/_moduleConfigLayout.php', ['space' => $space]) ?>

    <?php $form = ActiveForm::begin(); ?>

        <h4>
            <?= Yii::t('XcoinModule.config', 'Space scheduled transactions parameters'); ?>
        </h4>

        <div class="help-block">
            <?= Yii::t('XcoinModule.config', 'Each period of time, a transaction is created to a memeber\'s account.') ?>
        </div>

        <?= $form->field($model, 'transactionPeriod')->dropDownList([
                0 => 'Weekly',
                1 => 'Monthly'
        ]) ?>

        <hr>

    <?= Html::submitButton('Save', ['class' => 'btn btn-default', 'data-ui-loader' => '']) ?>

    <?php ActiveForm::end(); ?>


    <?php $this->endContent(); ?>

</div>