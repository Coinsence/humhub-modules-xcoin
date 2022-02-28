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

/** @var $projectplace Projectplace */
/** @var $assets Asset[] */
/** @var $defaultAsset Asset */
/** @var $isCreateForm bool */

/** @var $upload Upload */
$upload = Upload::forModel($projectplace, 'cover');
?>

<?php ModalDialog::begin([
    'header' => $isCreateForm ? Yii::t('XcoinModule.Projectplace', 'Create Projectplace') : Yii::t('XcoinModule.Projectplace', 'Edit Projectplace'),
    'closable' => false
]) ?>
<?php $form = ActiveForm::begin(['id' => 'project-place-form']); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($projectplace, 'title')->textInput()->hint(Yii::t('XcoinModule.Projectplace', 'Please enter your projectplace title')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($projectplace, 'description')->widget(RichTextField::class, ['preset' => 'full'])
                ->hint(Yii::t('XcoinModule.Projectplace', 'Please enter your projectplace description')) ?>
        </div>
        <div class="col-md-12">
            <?=
            $form->field($projectplace, 'invest_asset_id')->widget(Select2::class, [
                'data' => $assets,
                'options' => [
                    'placeholder' => '',
                    'disabled' => !$isCreateForm,
                ],
                'theme' => Select2::THEME_BOOTSTRAP,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ]);
            ?>
        </div>
        <div class="col-md-12">
            <?=
            $form->field($projectplace, 'reward_asset_id')->widget(Select2::class, [
                'data' => $assets,
                'options' => [
                    'placeholder' => '',
                    'disabled' => !$isCreateForm,
                ],
                'theme' => Select2::THEME_BOOTSTRAP,
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
                        'label' => $projectplace->getAttributeLabel('cover'),
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
                        'model' => $projectplace,
                        'showInStream' => true,
                        'edit' => false
                    ]) ?>
                </div>
            </div>
            <?php if ($projectplace->hasErrors('cover')): ?>
                <div class="has-error">
                    <?php foreach ($projectplace->getErrors('cover') as $error): ?>
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
