<?php

use humhub\libs\Html;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\AccountHelper;
?>
<?php ModalDialog::begin(['header' => '<strong>' . Html::encode($transaction->asset->title) . '</strong> - ' . Yii::t('XcoinModule.asset', 'Issue new'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'issue-form']); ?>
<div class="modal-body">
    <?= $form->field($transaction, 'to_account_id')->dropDownList($accounts); ?>
    <?= $form->field($transaction, 'amount'); ?>
    <?= $form->field($transaction, 'comment'); ?>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
