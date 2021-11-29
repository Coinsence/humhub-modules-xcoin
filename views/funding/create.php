<?php

use humhub\modules\xcoin\models\Funding;
use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\assets\Select2BootstrapAsset;
use yii\web\JsExpression;
use kartik\widgets\Select2;

/** @var $challengesList array */
/** @var $fundings array */
/** @var $model Funding */


Select2BootstrapAsset::register($this);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', 'Set funding request'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>
<?= Html::hiddenInput('step', '1'); ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?=
            $form->field($model, 'challenge_id')->widget(Select2::class, [
                'data' => $challengesList,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Select challenge') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => false,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->label(Yii::t('XcoinModule.funding', 'Challenge'));
            ?>
            <?php if (!empty($fundings)) : ?>
                <?=
                $form->field($model, 'clone_id')->widget(Select2::class, [
                    'data' => $fundings,
                    'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Select existing project') . ' - '],
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'hideSearch' => false,
                    'pluginOptions' => [
                        'allowClear' => false,
                        'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                    ],
                ])->label(Yii::t('XcoinModule.funding', 'Use Existing Project Data ?'))
                    ->hint(Yii::t('XcoinModule.funding', 'Leave empty to create a new Project Dataset'));
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'Next')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
