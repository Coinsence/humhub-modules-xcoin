<?php

use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\widgets\AmountField;
use yii\bootstrap\Html;
use yii\helpers\Url;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;
use humhub\assets\Select2BootstrapAsset;
use yii\web\JsExpression;
use kartik\widgets\Select2;

Select2BootstrapAsset::register($this);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', 'Load Wallet Private Key'), 'closable' => true]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'currentPassword')->passwordInput()->label(Yii::t('XcoinModule.funding', 'Current Password')); ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'Confirm')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
