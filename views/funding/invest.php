<?php

use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\models\FundingInvest;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\assets\Select2BootstrapAsset;
use humhub\modules\xcoin\widgets\AmountField;
use humhub\modules\xcoin\widgets\SenderAccountField;
use humhub\modules\space\widgets\Image as SpaceImage;

Select2BootstrapAsset::register($this);

/** @var Funding $funding */
/** @var FundingInvest $model */
?>
<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', '<strong>Funding</strong> Invest'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'asset-form']); ?>
<div class="modal-body">
    <div class="row text-center">
        <div class="col-md-12">
            <?= Yii::t('XcoinModule.transaction', '<strong> How many COINs you want to invest in this project ? </strong> <br> ') ?>
            <?= Yii::t('XcoinModule.transaction', 'The maximum number of COINs that can be invested here is {max_buy}', [
                'max_buy' => $model->getMaxBuyAmount()
            ]) ?>
        </div>
        <div class="col-lg-6 col-lg-offset-3">
            <?= $form->field($model, 'amountPay')->widget(AmountField::classname(), ['asset' => $model->getPayAsset()])->hint('')->label(''); ?>
        </div>
        <?php if (!$model->funding->challenge->acceptNoRewarding()): ?>
            <div class="col-md-12">
                <?= Yii::t('XcoinModule.transaction', '<strong> As rewarding , you will receive <br> {exchange_rate} for each coin you invest. </strong>', [
                    'exchange_rate' =>
                        $model->funding->exchange_rate .
                        '<span style="margin-left: 4px;">' . SpaceImage::widget([
                            'space' => $model->getBuyAsset()->space,
                            'width' => 16,
                            'showTooltip' => true,
                            'link' => true
                        ]) . '</span>'
                ]) ?>
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
