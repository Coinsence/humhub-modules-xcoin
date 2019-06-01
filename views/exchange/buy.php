<?php

use humhub\libs\Html;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\assets\Select2BootstrapAsset;
use humhub\modules\xcoin\widgets\AmountField;
use humhub\modules\xcoin\widgets\SenderAccountField;

Select2BootstrapAsset::register($this);
?>
<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.exchange', '<strong>Exchange</strong> asset'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'asset-form']); ?>
<div class="modal-body">
    <?= SenderAccountField::widget(['backRoute' => ['/xcoin/exchange/buy', 'exchangeId' => $exchange->id], 'senderAccount' => $fromAccount]); ?>
    <br />
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'amountBuy')->widget(AmountField::class, ['asset' => $exchange->asset])->label(Yii::t('XcoinModule.exchange', 'Buy')); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'amountPay')->widget(AmountField::class, ['asset' => $exchange->wantedAsset, 'readonly' => true]); ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

<script>
    $('#exchangebuy-amountbuy').on('input', function (e) {
        reCalc();
    });

    reCalc();
    function reCalc() {
        $val = $('#exchangebuy-amountbuy').val();
        $val = $val * <?= $model->exchange->exchange_rate; ?>;
        $val = (parseFloat($val).toPrecision(4));
        $('#exchangebuy-amountpay').val($val);

    }

</script>
