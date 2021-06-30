<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\file\widgets\Upload;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\ChallengeContactButton;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\assets\Select2BootstrapAsset;
use yii\web\JsExpression;
use kartik\widgets\Select2;

/** @var $model Challenge */
/** @var $assets Asset[] */
/** @var $contactButtons ChallengeContactButton[] */

Select2BootstrapAsset::register($this);

$upload = Upload::forModel($model, $model->coverFile);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.challenge', 'Edit Challenge'), 'closable' => false]) ?>
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
            <div class="col-md-1">
                <input type="checkbox" id="firstButton"
                       name="firstButton" <?= $contactButtons[0]->status != true ?: 'checked="checked"' ?>>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="firstButtonTitle" name="firstButtonTitle"
                       placeholder="Button title" value="<?= $contactButtons[0]->button_title ?>">
            </div>
            <div class="col-md-4">
                <input type="text" id="firstButtonText" class="form-control" name="firstButtonText"
                       placeholder="Text for popup" value="<?= $contactButtons[0]->popup_text ?>">
            </div>
            <div class="col-md-3">
                <select name="firstButtonReceiver" class="form-control select2" id="firstButtonReceiver">
                    <option value="" selected disabled><?= Yii::t('XcoinModule.challenge', 'Send message to') ?>to
                    </option>
                    <option value="challenge"<?= $contactButtons[0]->receiver != ChallengeContactButton::CHALLENGE_OWNER_RECEIVER ?: 'selected="selected"' ?>>
                        <?= Yii::t('XcoinModule.challenge', 'Challenge owner')?>
                    </option>
                    <option value="project"<?= $contactButtons[0]->receiver != ChallengeContactButton::PROJECT_OWNER_RECEIVER ?: 'selected="selected"' ?>>
                        <?= Yii::t('XcoinModule.challenge', 'Project owner')?>
                    </option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-1">
                <input type="checkbox" id="secondButton"
                       name="secondButton" <?= $contactButtons[1]->status != true ?: 'checked="checked"' ?>>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="secondButtonTitle" name="secondButtonTitle"
                       placeholder="Botton title" value="<?= $contactButtons[1]->button_title ?>">
            </div>
            <div class="col-md-4">
                <input type="text" id="secondButtonText" class="form-control" name="secondButtonText"
                       placeholder="Text for popup" value="<?= $contactButtons[1]->popup_text ?>">
            </div>
            <div class="col-md-3">
                <select name="secondButtonReceiver" class="form-control select2" id="secondButtonReceiver" required>
                    <option value="" selected
                            disabled><?= Yii::t('XcoinModule.challenge', 'Send message to') ?></option>
                    <option value="challenge"<?= $contactButtons[1]->receiver != ChallengeContactButton::CHALLENGE_OWNER_RECEIVER ?: 'selected="selected"' ?>>
                        <?= Yii::t('XcoinModule.challenge', 'Challenge owner')?>
                    </option>
                    <option value="project"<?= $contactButtons[1]->receiver != ChallengeContactButton::PROJECT_OWNER_RECEIVER ?: 'selected="selected"' ?>>
                        <?= Yii::t('XcoinModule.challenge', 'Project owner')?>
                    </option>
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <?=
            $form->field($model, 'stopped')->widget(Select2::class, [
                'data' => [
                    Challenge::CHALLENGE_ACTIVE => 'ACTIVE',
                    Challenge::CHALLENGE_STOPPED => 'STOPPED',
                    Challenge::CHALLENGE_CLOSED => 'CLOSED',
                ],
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.challenge', 'Select status') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
            ])->label(Yii::t('XcoinModule.challenge', 'Status'));
            ?>
        </div>
        <div class="col-md-12">
            <label class="control-label"><?= Yii::t('XcoinModule.challenge', 'Challenge Image') ?></label><br>
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
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.challenge', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
