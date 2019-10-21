<?php
/**
 * Created by Mortadha Ghanmi.
 * Email: mortadha.ghanmi56@gmail.com
 *
 * @var $this yii\web\View
 * @var $model \humhub\modules\xcoin\models\SpaceModuleScheduleSettings
 */

use \humhub\modules\xcoin\widgets\MemberPickerField;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="panel panel-default">

    <?php $this->beginContent('@xcoin/views/config/_moduleConfigLayout.php', ['space' => $space]) ?>

    <?php $form = ActiveForm::begin(); ?>

        <h4>
            <?= Yii::t('XcoinModule.config', 'Select users to allocate coins to'); ?>
        </h4>

        <div class="help-block">
            <?= Yii::t('XcoinModule.config', 'Each period of time, a transaction is created to a memeber\'s account.') ?>
        </div>

        <?= $form->field($model, 'selecAllMembers')->checkbox([
            'uncheck' => 0,
            'checked' => 1,
        ]) ?>

        <?= $form->field($model, 'selectedMembers')->widget(MemberPickerField::class, [
            'space' => $space,
            'placeholder' => Yii::t('XcoinModule.config', 'Add wanted users'),
        ]) ?>

        <hr>

        <?= Html::submitButton('Allocate', ['class' => 'btn btn-default', 'data-ui-loader' => '']) ?>

    <?php ActiveForm::end(); ?>


    <?php $this->endContent(); ?>

</div>