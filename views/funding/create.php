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

/** @var $assetList array */
/** @var $defaultAsset Asset */
/** @var $myAsset Asset */

Select2BootstrapAsset::register($this);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', 'Select wanted asset and define exchange rate'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>
<?= Html::hiddenInput('step', '1'); ?>
<div class="modal-body">
    <?= $form->field($model, 'amount')->widget(AmountField::class, ['asset' => $myAsset])->label(Yii::t('XcoinModule.funding', 'Offered amount')); ?>
    <p><?= Yii::t('XcoinModule.funding', 'Determine the exchange rate for which you are willing to trade assets.'); ?></p>
    <div class="row">
        <div class="col-md-5">
            <?= $form->field($model, 'exchange_rate')->widget(AmountField::class, ['asset' => $myAsset])->label(Yii::t('XcoinModule.funding', 'Provided asset')); ?>
        </div>
        <div class="col-md-2 text-center">
            <i class="fa fa-exchange colorSuccess" style="font-size:28px;padding-top:24px" aria-hidden="true"></i>
        </div>
        <div class="col-md-5">
            <?=
            $form->field($model, 'asset_id')->widget(Select2::classname(), [
                'data' => $assetList,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Select asset') . ' - ', 'value' => ($defaultAsset) ? $defaultAsset->id : []],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->label(Yii::t('XcoinModule.funding', 'Requested asset'));
            ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'Next')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
