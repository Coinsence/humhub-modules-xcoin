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

/**@var string $privateKey */
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', 'Load Wallet Private Key'), 'closable' => true]) ?>
<div class="modal-body">
    <div class="text-center form-group">
        <div class="alert alert-info" role="alert">
            <?= $privateKey ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= ModalButton::cancel(); ?>
</div>

<?php ModalDialog::end() ?>
