<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

use humhub\libs\Iso3166Codes;
use humhub\modules\ui\form\widgets\DatePicker;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Experience;
use kartik\widgets\Select2;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use yii\web\JsExpression;

/** @var $model Experience */

Assets::register($this);
?>

<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.experience', 'Provide experience details'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'experience-form']); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'position')->textInput()
                ->hint(Yii::t('XcoinModule.experience', 'Please enter the position')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'employer')->textInput()
                ->hint(Yii::t('XcoinModule.experience', 'Please enter the employer')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textarea(['rows' => 8])
                ->hint(Yii::t('XcoinModule.experience', 'Please enter a description for this experience')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'country')->widget(Select2::class, [
                'data' => iso3166Codes::$countries,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.experience', 'Select country') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => false,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->hint(Yii::t('XcoinModule.experience', 'Please enter the country')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'city')->textInput()
                ->hint(Yii::t('XcoinModule.experience', 'Please enter the city')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'actual_position')->checkbox() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'start_date')->widget(DatePicker::class, [
                'dateFormat' => 'yyyy-MM',
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'showButtonPanel' => true,
                    'onClose' => new JsExpression("function(dateText, inst) { 
                         $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                    }")
                ],
                'options' => ['class' => 'form-control', 'autocomplete' => "off"]])
                ->hint(Yii::t('XcoinModule.experience', 'When was this experience started')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'end_date')->widget(DatePicker::class, [
                'dateFormat' => 'yyyy-MM',
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'showButtonPanel' => true,
                    'onClose' => new JsExpression("function(dateText, inst) { 
                         $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                    }")
                ],
                'options' => ['class' => 'form-control', 'autocomplete' => "off", 'value' => 'present', 'disabled' => $model->actual_position == 1]])
                ->hint(Yii::t('XcoinModule.experience', 'When was this experience ended')) ?>
        </div>
    </div>
</div>
<hr>
<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.experience', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

