<?php

use humhub\libs\Iso3166Codes;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\xcoin\models\Asset;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\DatePicker;
use yii\web\JsExpression;

/** @var $myAsset Asset */

?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', 'Provide details'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>


<div class="modal-body">
    <div class="row">
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
        <div class="col-md-6">
            <?= $form->field($model, 'country')->widget(Select2::class, [
                'data' => iso3166Codes::$countries,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Select country') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => false,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign country')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'city')->textInput()
                ->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign city')) ?>
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
