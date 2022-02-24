<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2022 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\file\widgets\Upload;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Projectplace;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\assets\Select2BootstrapAsset;
use yii\web\JsExpression;
use kartik\widgets\Select2;
use humhub\modules\xcoin\assets\Assets;
use yii\widgets\ActiveForm;

Select2BootstrapAsset::register($this);
Assets::register($this);

/** @var $model Projectplace */
/** @var $assets Asset[] */
/** @var $defaultAsset Asset */

$upload = Upload::forModel($model, 'cover');
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.Projectplace', 'Create Projectplace'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'project-place-form']); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'title')->textInput()->hint(Yii::t('XcoinModule.Projectplace', 'Please enter your projectplace title')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->widget(RichTextField::class, ['preset' => 'full'])
                ->hint(Yii::t('XcoinModule.Projectplace', 'Please enter your projectplace description')) ?>
        </div>
        <div class="col-md-12">
            <?=
            $form->field($model, 'invest_asset_id')->widget(Select2::class, [
                'data' => $assets,
                'options' => ['placeholder' => ''],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ]);
            ?>
        </div>
        <div class="col-md-12">
            <?=
            $form->field($model, 'reward_asset_id')->widget(Select2::class, [
                'data' => $assets,
                'options' => ['placeholder' => ''],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ]);
            ?>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-2">
                    <?= $upload->button([
                        'label' => $model->getAttributeLabel('cover'),
                        'tooltip' => false,
                        'options' => ['accept' => 'image/*'],
                        'cssButtonClass' => 'btn-default btn-sm',
                        'dropZone' => '#product-form',
                        'single' => true,
                    ]) ?>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-9">
                    <?= $upload->preview([
                        'options' => ['style' => 'margin-top:5px'],
                        'model' => $model,
                        'showInStream' => false,
                    ]) ?>
                </div>
            </div>
            <div class="help-block">
                <?= Yii::t('XcoinModule.Projectplace', 'Please note that first picture will be used as cover (MAXIMUM FILE SIZE IS 500kb).') ?>
            </div>
            <?php if ($model->hasErrors('cover')): ?>
                <div class="has-error">
                    <?php foreach ($model->getErrors('cover') as $error): ?>
                        <div class="help-block"><?= $error ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?= $upload->progress() ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.Projectplace', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
