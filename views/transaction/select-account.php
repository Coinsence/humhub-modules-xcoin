<?php

use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\modules\xcoin\grids\SenderAccountGridView;

if (!isset($requireAsset)) {
    $requireAsset = null;
}
if (!isset($disableAccount)) {
    $disableAccount = null;
}
?>
<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.transaction', '<strong>Select</strong> sender account'), 'closable' => false]) ?>
<div class="modal-body">
<?= SenderAccountGridView::widget([
    'contentContainer' => $contentContainer, 
    'nextRoute' => $nextRoute,
    'requireAsset' => $requireAsset,
    'disableAccount' => $disableAccount
    ])
?>
</div>

<div class="modal-footer">
<?= ModalButton::cancel(); ?>
</div>

<?php ModalDialog::end() ?>
