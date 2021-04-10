<?php
/**
 * Created by Mortadha Ghanmi.
 * Email: mortadha.ghanmi56@gmail.com
 *
 * @var $this yii\web\View
 * @var $model \humhub\modules\xcoin\models\SpaceModuleAppearanceSettings
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="panel panel-default">

    <?php $this->beginContent('@xcoin/views/config/_moduleConfigLayout.php', ['space' => $space]) ?>

    <?php $form = ActiveForm::begin(); ?>

        <h4>
            <?= Yii::t('XcoinModule.config', 'Enable/disable space appearance settings'); ?>
        </h4>

        <div class="help-block">
            <?= Yii::t('XcoinModule.config', 'Various settings for the space appearance') ?>
        </div>

        <?= $form->field($model, 'partiallyHideCover')->checkbox([
            'uncheck' => 0,
            'checked' => 1,
        ]) ?>

        <hr>

        <?= Html::submitButton('Save', ['class' => 'btn btn-default', 'data-ui-loader' => '']) ?>

    <?php ActiveForm::end(); ?>


    <?php $this->endContent(); ?>

</div>
