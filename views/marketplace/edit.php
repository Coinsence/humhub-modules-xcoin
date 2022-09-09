<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\file\widgets\Upload;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Category;
use humhub\modules\xcoin\models\Marketplace;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\assets\Select2BootstrapAsset;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use kartik\widgets\Select2;

/** @var $model Marketplace */
/** @var $assets Asset[] */
/** @var $imageError string */

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
            <?= $form->field($model, 'hide_unverified_submissions')->checkbox(); ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'hidden')->checkbox(); ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'categories_names')->widget(Select2::class, [
                'model' => $model,
                'attribute' => 'categories_names',
                'data' => ArrayHelper::map(Category::find()->where(['type' => Category::TYPE_MARKETPLACE])->all(), 'name', 'name'),
                'options' => [
                    'multiple' => true,
                ],
                'pluginOptions' => [
                    'tokenSeparators' => [',', ' '],
                ],
            ])->label(Yii::t('XcoinModule.marketplace', 'Categories')); ?>
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
            $form->field($model, 'selling_option')->widget(Select2::class, [
                'data' => Marketplace::getOptions(),
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
            <label class="control-label"><?= Yii::t('XcoinModule.marketplace', 'Marketplace Image (MAXIMUM FILE SIZE IS 500kb)') ?></label><br>
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
        <div class="col-md-12">
            <?php if ($imageError) : ?>
                <p class="help-block help-block-error" style="color:red"><?= Yii::t('XcoinModule.challenge', $imageError) ?></p>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.marketplace', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
