<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\widgets\AmountField;
use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\DatePicker;

/** @var $myAsset Asset */

?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', 'Provide details'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>

<?= Html::hiddenInput('step', '2'); ?>

<?= $form->field($model, 'asset_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'amount')->widget(AmountField::class, ['asset' => $model->asset, 'readonly' => $model->isNewRecord? false : true])->label(Yii::t('XcoinModule.funding', 'Requested amount')); ?>
        </div>
        <div class="row col-md-12">
            <div class="col-md-5">
                <?= $form->field($model, 'exchange_rate')->widget(AmountField::class, ['asset' => $myAsset, 'readonly' => $model->isNewRecord? false : true])->label(Yii::t('XcoinModule.base', 'Provided Coins')); ?>
            </div>
            <div class="col-md-2 text-center">
                <i class="fa fa-exchange colorSuccess" style="font-size:28px;padding-top:24px" aria-hidden="true"></i>
            </div>
            <div class="col-md-5">
                <?= $form->field($model, 'rate')->widget(AmountField::class, ['asset' => $model->asset, 'readonly' => true])->label(Yii::t('XcoinModule.base', 'Requested Coins')); ?>
            </div>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'title')->textInput()
                ->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign title')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'deadline')->widget(DatePicker::class, [
                'dateFormat' => Yii::$app->params['formatter']['defaultDateFormat'],
                'clientOptions' => ['minDate' => '+1d'],
                'options' => ['class' => 'form-control', 'autocomplete' => "off"]])
                ->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign deadline')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textarea(['maxlength' => 255])
                ->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign description')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'content')->widget(RichTextField::class, ['preset' => 'full'])
                ->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign needs & commitments')) ?>
        </div>
        <?php if (!$model->isNewRecord): ?>
            <div class="row">
                <div class="col-md-6 text-center">
                    <?= Html::a(Yii::t('XcoinModule.base', 'Accept investment'), ['accept', 'id' => $model->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-success', 'style' => 'margin-bottom: 10px;', 'data-modal-close' => '']); ?>
                </div>
                <div class="col-md-6 text-center">
                    <?= Html::a(Yii::t('XcoinModule.base', 'Cancel this campaign'), ['cancel', 'id' => $model->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-danger', 'data-modal-close' => '']); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<hr>
<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'Next')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

