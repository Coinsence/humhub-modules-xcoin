<?php

use yii\bootstrap\Html;
use yii\helpers\Url;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
?>

<?php ModalDialog::begin(['header' => 'Create/Edit Asset', 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'asset-form']); ?>

<div class="modal-body">
    <?= $form->field($asset, 'title'); ?>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, 'Save'); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
