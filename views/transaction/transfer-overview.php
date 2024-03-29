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
    <div class="row text-center">
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
    <div class="row text-center" style="margin-top: 20px">
        <div class="col-md-12">
            <?= ModalButton::submitModal(null, Yii::t('XcoinModule.transaction', 'Account')) ?>
            <?= ModalButton::cancel(Yii::t('XcoinModule.transaction', 'Close')) ?>
        </div>
    </div>
</div>

<div class="modal-footer">
        <div class="row text-center">
            <div class="col-md-12">
                <?= Yii::t('XcoinModule.transaction', 'link to blockchain transaction: ') ?><br>
                <?=
                Html::a(
                    StringHelper::truncate($transaction->algorand_tx_id, 30, '...'),
                    " https://algoexplorer.io/tx/$transaction->algorand_tx_id",
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
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
