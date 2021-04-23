<?php

use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\assets\Select2BootstrapAsset;

Select2BootstrapAsset::register($this);

/**@var string $privateKey */
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', 'Load Wallet Private Key'), 'closable' => true]) ?>
<div class="modal-body">
    <div class="text-center form-group">
        <div class="alert alert-info wallet-pkey" role="alert">
            <?= $privateKey ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= ModalButton::cancel(); ?>
</div>

<?php ModalDialog::end() ?>
