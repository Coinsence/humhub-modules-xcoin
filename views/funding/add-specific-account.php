<?php

use humhub\modules\file\widgets\Upload;
use humhub\modules\xcoin\models\Account;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

$upload = Upload::withName();

/**
 * @var $spaceId integer
 */
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', 'Add Account from where investors will get coins'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>

<?= Html::hiddenInput('step', '4'); ?>

<?= $form->field($model, 'challenge_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'amount', ['enableError' => false])->hiddenInput()->label(false)->hint(false) ?>
<?= $form->field($model, 'exchange_rate', ['enableError' => false])->hiddenInput()->label(false)->hint(false) ?>
<?= $form->field($model, 'categories_names', ['enableError' => false])->hiddenInput()->label(false)->hint(false) ?>
<?= $form->field($model, 'title')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'description')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'content')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'country')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'city')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'deadline')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'specific_sender_account_id')->widget(Select2::className(), [
                'attribute' => 'Sender Account',
                'data' => ArrayHelper::map(Account::find()->where(['space_id' => $spaceId])->all(), 'id', 'title'),
            ])->label(true); ?>
        </div>
    </div>
</div>
<hr>
<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'save')); ?>
    <?= ModalButton::cancel(); ?>
</div>
<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

