<?php

use humhub\assets\Select2BootstrapAsset;
use humhub\libs\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\widgets\AccountField;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use kartik\widgets\Select2;
use yii\web\JsExpression;

Select2BootstrapAsset::register($this);
?>
<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.transaction', '<strong>Transfer</strong> asset'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'asset-form']); ?>
<div class="modal-body">
    <div class="form-group">
        <label class="control-label"><?= Yii::t('XcoinModule.transaction', 'Sender account') ?></label>
        <div class="form-control" style="padding-top:4px;">
            <?php if ($fromAccount->space !== null): ?>
                <?= SpaceImage::widget(['space' => $fromAccount->space, 'width' => 24]); ?>
            <?php endif; ?>
            <?php if ($fromAccount->user !== null): ?>
                <?= UserImage::widget(['user' => $fromAccount->user, 'width' => 24]); ?>
            <?php endif; ?>
            <?= $fromAccount->title; ?>
            <?= Html::a('Change', ['/xcoin/transaction/select-account', 'container' => $this->context->contentContainer], ['class' => 'btn btn-sm btn-default pull-right', 'style' => 'margin-top:-2px;margin-right:-10px', 'data-target' => '#globalModal', 'data-ui-loader' => '']); ?>
        </div>
    </div>

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
    <?= $form->field($transaction, 'to_account_id')->widget(AccountField::class); ?>
    <hr/>
    <?= $form->field($transaction, 'comment'); ?>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
