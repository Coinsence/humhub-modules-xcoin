<?php

use humhub\assets\Select2BootstrapAsset;
use humhub\libs\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\models\Transaction;
use humhub\modules\xcoin\widgets\AccountField;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/**
 * @var $transaction Transaction
 */
Select2BootstrapAsset::register($this);
?>
<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.transaction', '<strong>Transfer</strong> asset'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'asset-form']); ?>
<?= Html::hiddenInput('step', '2'); ?>
<div class="modal-body">
    <hr class="row">
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($transaction, 'amount'); ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($transaction, 'asset_id')->widget(Select2::class, [
                'data' => $accountAssetList,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.transaction', 'Select asset') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ]);
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
