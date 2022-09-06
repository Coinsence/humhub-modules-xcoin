<?php

use humhub\assets\Select2BootstrapAsset;
use humhub\libs\StringHelper;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use yii\helpers\Html;

Select2BootstrapAsset::register($this);

/** @var $amountPay number */
/** @var $amountBuy number */
/** @var $payAsset Asset */
/** @var $buyAsset Asset */
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', '<strong>Transfer</strong> successfull'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'asset-form']); ?>
<?= Html::hiddenInput('overview', '1'); ?>
<div class="modal-body">
    <div class="row text-center">
        <div class="col-md-12">
            <?= Yii::t('XcoinModule.transaction', 'Your transfer of {amount} was successfull', [
                'amount' =>
                    $amountPay .
                    '<span style="margin-left: 4px;">' . SpaceImage::widget([
                        'space' => $payAsset->space,
                        'width' => 16,
                        'showTooltip' => true,
                        'link' => true
                    ]) . '</span>'
            ]) ?>
        </div>
    </div>

    <?php if ($buyAsset) : ?>
        <div class="row text-center" style="margin-top: 20px">
            <div class="col-md-12">
                <?= Yii::t('XcoinModule.transaction', 'You\'ve recieved {amount}', [
                    'amount' =>
                        $amountBuy .
                        '<span style="margin-left: 4px;">' . SpaceImage::widget([
                            'space' => $buyAsset->space,
                            'width' => 16,
                            'showTooltip' => true,
                            'link' => true
                        ]) . '</span>'
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row text-center" style="margin-top: 20px">
        <div class="col-md-12">
            <?= ModalButton::submitModal(null, Yii::t('XcoinModule.transaction', 'Account')) ?>
            <?= ModalButton::cancel(Yii::t('XcoinModule.transaction', 'Close')) ?>
        </div>
    </div>

</div>
<div class="modal-footer">
    <?php if ($transaction->algorand_tx_id) : ?>
        <div class="row text-center">
            <div class="col-md-12">
                <?= Yii::t('XcoinModule.transaction', 'link to blockchain transaction: ') ?><br>
                <?=
                Html::a(
                    StringHelper::truncate($transaction->algorand_tx_id, 30, '...'),
                    " https://testnet.algoexplorer.io/tx/$transaction->algorand_tx_id",
                    [
                        'target' => '_blank',
                        'title' => $transaction->algorand_tx_id,
                        'data-toggle' => 'tooltip',
                        'style' => 'color: #3cbeef; margin-top: 4px;',
                    ]
                )
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
