<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\file\widgets\Upload;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Challenge;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\assets\Select2BootstrapAsset;
use yii\web\JsExpression;
use kartik\widgets\Select2;
use humhub\modules\xcoin\assets\Assets;

/** @var $model Challenge */
/** @var $assets Asset[] */
/** @var $defaultAsset Asset */
/** @var $imageError string */

Select2BootstrapAsset::register($this);
Assets::register($this);

$upload = Upload::forModel($model, $model->coverFile);

?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.challenge', 'Create Challenge'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'challenge-form']); ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'title')->textInput()->hint(Yii::t('XcoinModule.challenge', 'Please enter your challenge title')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->widget(RichTextField::class, ['preset' => 'full'])
                ->hint(Yii::t('XcoinModule.challenge', 'Please enter your challenge description')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'hide_unverified_submissions')->checkbox(); ?>
        </div>
        <div class="col-md-12">
            <?=
            $form->field($model, 'asset_id')->widget(Select2::class, [
                'data' => $assets,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.challenge', 'Select coin') . ' - ', 'value' => ($defaultAsset) ? $defaultAsset->id : ''],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->label(Yii::t('XcoinModule.funding', 'Requested coin'));
            ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'any_reward_asset')->radio(); ?>
            <?= $form->field($model, 'no_rewarding')->radio(); ?>
            <?= $form->field($model, 'specific_reward_asset')->radio(); ?>
            <div class="col-md-12">
                <div class="col-md-4">
                    <?= $form->field($model, 'exchange_rate')->textInput(['maxlength' => true, 'style' => 'visibility:hidden'
                        , 'placeholder' => 'Set Number'])->label(false) ?>
                </div>
                <div class="col-md-2">
                    <label class="control-label " for="challenge-exchange_rate" style="visibility:hidden "
                           id="challenge-label_exchange_rate"><?= Yii::t('XcoinModule.challenge', 'of') ?></label>
                </div>
                <div class="col-md-4">
                    <?=
                    $form->field($model, 'specific_reward_asset_id')->widget(Select2::class, [
                        'data' => $assets,
                        'options' => [
                            'placeholder' => '- ' . Yii::t('XcoinModule.challenge', 'Select coin') . ' - ', 'value' => ($defaultAsset) ? $defaultAsset->id : '',
                            'class' => 'hide',
                        ],
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => false,
                            'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                        ],
                    ])->label(false);
                    ?>
                </div>
                <div class="col-md-2">
                    <label class="control-label " style="visibility:hidden"
                           id="challenge-label_specific_reward_asset"><?= Yii::t('XcoinModule.challenge', 'for each invested COIN') ?></label>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-1">
                <input type="checkbox" id="firstButton" name="firstButton">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="firstButtonTitle" name="firstButtonTitle"
                       placeholder="Botton title">
            </div>
            <div class="col-md-4">
                <input type="text" id="firstButtonText" class="form-control" name="firstButtonText"
                       placeholder="Text for popup">
            </div>
            <div class="col-md-3">
                <select name="firstButtonReceiver" class="form-control select2" id="firstButtonReceiver">
                    <option value="" disabled selected>Send message to</option>
                    <option value="challenge">Challenge owner</option>
                    <option value="project">Project owner</option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-1">
                <input type="checkbox" id="secondButton" name="secondButton">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="secondButtonTitle" name="secondButtonTitle"
                       placeholder="Botton title">
            </div>
            <div class="col-md-4">
                <input type="text" id="secondButtonText" class="form-control" name="secondButtonText"
                       placeholder="Text for popup">
            </div>
            <div class="col-md-3">
                <select name="secondButtonReceiver" class="form-control select2" id="secondButtonReceiver" required>
                    <option value="" disabled selected>Send message to</option>
                    <option value="challenge">Challenge owner</option>
                    <option value="project">Project owner</option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <label class="control-label"><?= Yii::t('XcoinModule.challenge', 'Challenge Image (MAXIMUM FILE SIZE IS 500kb)') ?></label><br>
            <div class="col-md-2">
                <?= $upload->button([
                    'label' => true,
                    'tooltip' => false,
                    'options' => ['accept' => 'image/*'],
                    'cssButtonClass' => 'btn-default btn-sm',
                    'dropZone' => '#challenge-form',
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
        <div class="col-md-12">
            <?= $form->field($model, 'hidden_description')->checkbox(); ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'with_location_filter')->checkbox(); ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.challenge', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
