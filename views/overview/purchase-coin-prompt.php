<?php

use humhub\libs\Iso3166Codes;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use kartik\select2\Select2;
use yii\web\JsExpression;

/** @var $coin string */
/** @var $model humhub\modules\xcoin\models\forms\PurchaseForm */

?>

<?php ModalDialog::begin(['header' => 'Buy ' . $coin, 'closable' => true]) ?>
<?php $form = ActiveForm::begin(['id' => 'purchase-form']); ?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'amount')
                ->textInput(['type' => 'number', 'min' => 10])
                ->label(Yii::t('XcoinModule.purchase', 'How many COINs you want to buy ?'))
                ->hint(Yii::t('XcoinModule.purchase', 'Minimum amount of COINs is 10')); ?>
            <p><?= Yii::t('XcoinModule.purchase', 'The total price will be shown in the next step.') ?></p>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'coin')->textInput(['type' => 'text', 'readonly' => true])->label(Yii::t('XcoinModule.purchase', 'Selected COIN')); ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'address')->textInput(['type' => 'text'])->label(Yii::t('XcoinModule.purchase', 'Address')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'state')->textInput(['type' => 'text'])->label(Yii::t('XcoinModule.purchase', 'State')); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'city')->textInput(['type' => 'text'])->label(Yii::t('XcoinModule.purchase', 'City')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'zip')->textInput(['type' => 'text'])->label(Yii::t('XcoinModule.purchase', 'Postcode')); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'country')->widget(Select2::class, [
                'data' => Iso3166Codes::$countries,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.purchase', 'Select country') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => false,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ]) ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.purchase', 'Continue to the payment process')); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
