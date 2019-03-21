<?php

use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\DatePicker;

?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.base', 'Provide details'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>

<?= Html::hiddenInput('step', '3'); ?>
<?= $form->field($model, 'asset_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'available_amount', ['enableError' => false])->hiddenInput()->label(false)->hint(false) ?>
<?= $form->field($model, 'exchange_rate', ['enableError' => false])->hiddenInput()->label(false) ?>
<?= $form->field($model, 'amount')->hiddenInput()->label(false) ?>


<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'title')->textInput()
                ->hint('Please enter your crowdfunding campaign title')
                ->label('Title') ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textarea()
                ->hint('Please enter your crowdfunding campaign description')
                ->label('Description'); ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'needs')->textarea()
                ->hint('Please enter your crowdfunding campaign needs')
                ->label('Needs'); ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'commitments')->textarea()
                ->hint('Please enter your crowdfunding campaign need')
                ->label('Commitments'); ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'deadline')->widget(DatePicker::class, ['dateFormat' => Yii::$app->params['formatter']['defaultDateFormat'], 'clientOptions' => [], 'options' => ['class' => 'form-control', 'autocomplete' => "off"]]) ?>
        </div>
    </div>

    <?php if (!$model->isNewRecord): ?>
        <?= Html::a(Yii::t('XcoinModule.base', 'Delete this exchange request'), ['delete', 'id' => $model->id, 'container' => $this->context->contentContainer], ['class' => 'pull-right colorDanger']); ?>
    <?php endif; ?>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('base', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

