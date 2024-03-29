<?php

use yii\bootstrap\Html;
use yii\helpers\Url;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;

?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.account', 'Create/Edit Account'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>

<div class="modal-body">
    <?= $form->field($account, 'title'); ?>
    <?php if ($account->isNewRecord && empty($account->user_id)) : ?>
        <?=
        $form->field($account, 'editFieldManager')->widget(UserPickerField::className(), [
            'maxSelection' => 1,
        ]);
        ?>
    <?php endif; ?>

    <?= Html::error($account, 'algorand_address', ['class' => 'error-block', 'style' => 'color:red']); ?>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.account', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>
<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
