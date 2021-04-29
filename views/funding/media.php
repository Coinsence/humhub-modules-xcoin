<?php

use humhub\modules\file\widgets\Upload;
use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;

$upload = Upload::withName();
/**
 * @var $test string
 */
/**
 * @var $lastStepEnabled boolean
 */
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', 'Gallery'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>

<?= Html::hiddenInput('step', '3'); ?>

<?= $form->field($model, 'challenge_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'amount', ['enableError' => false])->hiddenInput()->label(false)->hint(false) ?>
<?= $form->field($model, 'exchange_rate', ['enableError' => false])->hiddenInput()->label(false)->hint(false) ?>
<?= $form->field($model, 'title')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'description')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'content')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'country')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'city')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'deadline')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>
<?php if ($model->categories_names): ?>
    <?= $form->field($model, 'categories_names')->hiddenInput(['value' => implode(",", $model->categories_names)])->label(false) ?>
<?php endif; ?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-2">
                    <?= $upload->button([
                        'label' => true,
                        'tooltip' => false,
                        'options' => ['accept' => 'image/*'],
                        'cssButtonClass' => 'btn-default btn-sm',
                        'dropZone' => '#account-form',
                        'max' => 7,
                    ]) ?>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-9">
                    <?= $upload->preview([
                        'options' => ['style' => 'margin-top:10px'],
                        'model' => $model,
                        'showInStream' => true,
                    ]) ?>
                </div>
            </div>
            <br>
            <?= $upload->progress() ?>
            <p class="help-block">
                <?= Yii::t('XcoinModule.funding', 'Please note that first picture will be used as cover for your crowdfunding campaign.') ?>
            </p>
        </div>
    </div>
</div>
<hr>
<?php if ($lastStepEnabled): ?>
    <div class="modal-footer">
        <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'Next')); ?>
        <?= ModalButton::cancel(); ?>
    </div>
<?php else: ?>
    <div class="modal-footer">
        <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'save')); ?>
        <?= ModalButton::cancel(); ?>
    </div>
<?php endif; ?>
<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

