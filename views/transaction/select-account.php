<?php

use humhub\modules\content\models\ContentContainer;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\modules\xcoin\grids\SenderAccountGridView;
use humhub\modules\xcoin\widgets\PurchaseCoin;

/** @var ContentContainer $contentContainer */
/** @var array $nextRoute */
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.transaction', '<strong>Select</strong> sender account'), 'closable' => false]) ?>
<div class="modal-body">
    <?= SenderAccountGridView::widget([
        'contentContainer' => $contentContainer,
        'nextRoute' => $nextRoute,
        'requireAsset' => isset($requireAsset) ? $requireAsset : null,
        'disableAccount' => isset($disableAccount) ? $disableAccount : null,
        'product' => isset($product) ? $product : null,
    ])
    ?>
</div>

<div class="modal-footer">
    <?= PurchaseCoin::widget() ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ModalDialog::end() ?>
