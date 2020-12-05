<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\file\widgets\Upload;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Marketplace;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\assets\Select2BootstrapAsset;
use yii\web\JsExpression;
use kartik\widgets\Select2;

/** @var $model Marketplace */
/** @var $assets Asset[] */

Select2BootstrapAsset::register($this);

$upload = Upload::forModel($model, $model->coverFile);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.marketplace', 'Edit Marketplace'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'marketplace-form']); ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'title')->textInput()->hint(Yii::t('XcoinModule.marketplace', 'Please enter your marketplace title')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->widget(RichTextField::class, ['preset' => 'full'])
                ->hint(Yii::t('XcoinModule.marketplace', 'Please enter your marketplace description')) ?>
        </div>
        <div class="col-md-12">
            <?=
            $form->field($model, 'asset_id')->widget(Select2::class, [
                'data' => $assets,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.marketplace', 'Select coin') . ' - ', 'value' => $model->asset_id],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->label(Yii::t('XcoinModule.marketplace', 'Sales coin'));
            ?>
        </div>
        <div class="col-md-12">
            <?=
            $form->field($model, 'action_name')
                ->textInput()
                ->hint(Yii::t('XcoinModule.marketplace', 'Please enter product call to action button name, default is "Buy Product"'))
            ?>
        </div>
        <div class="col-md-12">
            <?=
            $form->field($model, 'is_link_required')->widget(Select2::class, [
                'data' => [
                    0 => Yii::t('XcoinModule.marketplace', 'Optional'),
                    1 => Yii::t('XcoinModule.marketplace', 'Required')
                ],
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.marketplace', 'Select product call to action link option') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
            ]);
            ?>
        </div>
        <div class="col-md-12">
            <?=
            $form->field($model, 'stopped')->widget(Select2::class, [
                'data' => [
                    Marketplace::MARKETPLACE_ACTIVE => 'OPEN',
                    Marketplace::MARKETPLACE_STOPPED => 'CLOSED'
                ],
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.marketplace', 'Select status') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
            ])->label(Yii::t('XcoinModule.marketplace', 'Status'));
            ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'is_tasks_marketplace')->checkbox() ?>
        </div>
        <div class="col-md-12">
            <label class="control-label"><?= Yii::t('XcoinModule.marketplace', 'Marketplace Image') ?></label><br>
            <div class="col-md-2">
                <?= $upload->button([
                    'label' => true,
                    'tooltip' => false,
                    'options' => ['accept' => 'image/*'],
                    'cssButtonClass' => 'btn-default btn-sm',
                    'dropZone' => '#marketplace-form',
                    'max' => 1,
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
            <br>
            <?= $upload->progress() ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.marketplace', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
