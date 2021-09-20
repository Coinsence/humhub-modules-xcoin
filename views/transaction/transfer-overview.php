<?php

use humhub\assets\Select2BootstrapAsset;
use humhub\libs\StringHelper;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Transaction;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use yii\helpers\Html;

Select2BootstrapAsset::register($this);

/** @var $transaction Transaction */
/** @var $asset Asset */
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.transaction', '<strong>Transfer</strong> successfull'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'asset-form']); ?>
<?= Html::hiddenInput('overview', '1'); ?>
<div class="modal-body">
    <div class="row" style="margin-bottom: 12px;">
        <div class="col-md-12">
            <?= Yii::t('XcoinModule.transaction', 'Your transfer of {amount} was successfull', [
                'amount' =>
                    $transaction->amount . 
                    '<span style="margin-left: 4px;">' . SpaceImage::widget([
                        'space' => $asset->space,
                        'width' => 16,
                        'showTooltip' => true,
                        'link' => true
                    ]) . '</span>'
            ]) ?>
        </div>
    </div>
    
    <?php if ($transaction->eth_hash) : ?>
        <div class="row">
            <div class="col-md-12">
                <?= 
                    Yii::t('XcoinModule.transaction', 'You can check this link to see the transaction hash on the blockchain: ') . 
                    Html::a(
                        StringHelper::truncate($transaction->eth_hash, 30, '...'),
                        " https://rinkeby.etherscan.io/tx/$transaction->eth_hash",
                        [
                            'target' => '_blank',
                            'title' => $transaction->eth_hash,
                            'data-toggle' => 'tooltip',
                            'style' => 'color: #3cbeef; margin-top: 4px;',
                        ]
                    )
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="modal-footer">
    <?= ModalButton::cancel(Yii::t('XcoinModule.transaction', 'Close')) ?>
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.transaction', 'Account')) ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
