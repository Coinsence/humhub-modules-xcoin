<?php

use humhub\libs\Iso3166Codes;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\xcoin\models\forms\CashOutForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use kartik\select2\Select2;
use yii\web\JsExpression;

/** @var $cashOutAssetName string */
/** @var $model CashOutForm */

?>

<?php ModalDialog::begin(['header' => 'Cash out ' . $cashOutAssetName, 'closable' => true]) ?>
<?php $form = ActiveForm::begin(['id' => 'cashout-form']); ?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'cashOutAssetName')->textInput(['type' => 'text', 'readonly' => true])->label(Yii::t('XcoinModule.purchase', 'Selected COIN')); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'min' => 10, 'max' => $model->getCoinBalance()]); ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'accountOwner')->textInput(['type' => 'text']); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'bankName')->textInput(['type' => 'text']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'bic')->textInput(['type' => 'text']); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'swift')->textInput(['type' => 'text']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'iban')->textInput(['type' => 'text']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'address')->textInput(['type' => 'text']); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'state')->textInput(['type' => 'text']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'city')->textInput(['type' => 'text']); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'zipCode')->textInput(['type' => 'text']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'country')->widget(Select2::class, [
                'data' => Iso3166Codes::$countries,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.purchase', 'Select country') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => false,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.forms_CashOutForm', 'Cash Out')); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
