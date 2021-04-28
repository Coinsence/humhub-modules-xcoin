<?php

use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\models\FundingInvest;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\assets\Select2BootstrapAsset;
use humhub\modules\xcoin\widgets\AmountField;
use humhub\modules\xcoin\widgets\SenderAccountField;

Select2BootstrapAsset::register($this);

/** @var Funding $funding */
/** @var FundingInvest $model */
?>
<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', '<strong>Funding</strong> Invest'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'asset-form']); ?>
<div class="modal-body">
    <?= SenderAccountField::widget(['backRoute' => ['/xcoin/funding/invest', 'fundingId' => $funding->id, 'container' => $this->context->contentContainer], 'senderAccount' => $fromAccount]); ?>
    <br/>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'amountPay')->widget(AmountField::classname(), ['asset' => $model->getPayAsset()]); ?>
        </div>
        <?php if ($model->getBuyAsset()): ?>
            <div class="col-md-6">
                <?= $form->field($model, 'amountBuy')->widget(AmountField::classname(), ['asset' => $model->getBuyAsset(), 'readonly' => true]); ?>
            </div>
        <?php endif; ?>


    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'Pay now')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

<script>
    $('#fundinginvest-amountpay').on('input', function (e) {
        reCalc();
    });

    reCalc();

    function reCalc() {
        $val = $('#fundinginvest-amountpay').val();
        $val = $val * <?= $model->funding->exchange_rate; ?>;
        $val = (parseFloat($val).toPrecision(6));
        $('#fundinginvest-amountbuy').val($val);

    }

</script>
