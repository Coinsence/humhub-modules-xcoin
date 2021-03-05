<?php

use humhub\modules\file\widgets\Upload;
use humhub\modules\xcoin\models\Product;
use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;

/** @var $model Product */

$upload = Upload::withName();
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.product', 'Gallery'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'product-form']); ?>

<?= Html::hiddenInput('step', '3'); ?>

<?= $form->field($model, 'marketplace_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'price', ['enableError' => false])->hiddenInput()->label(false)->hint(false) ?>
<?= $form->field($model, 'name')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'description')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'content')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'country')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'city')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'offer_type')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'discount')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'payment_type')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'product_type')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'link')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'buy_message')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'payment_first')->hiddenInput()->label(false)?>

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
                        'dropZone' => '#product-form',
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
                <?= Yii::t('XcoinModule.product', 'Please note that first picture will be used as cover.') ?>
            </p>
        </div>
    </div>
</div>
<hr>
<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.product', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

