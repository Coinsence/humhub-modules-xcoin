<?php
/** @var Funding $funding */

/** @var ChallengeContactButtonAlias $contactButton */

use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;
use humhub\modules\user\widgets\UserPickerField;
use humhub\modules\xcoin\models\ChallengeContactButton as ChallengeContactButtonAlias;
use humhub\modules\xcoin\models\Funding;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use yii\helpers\Html;
use humhub\modules\content\widgets\richtext\RichTextField;
use yii\helpers\Url;

?>


<div class="modal-dialog">
    <div class="modal-content">
        <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"
                id="myModalLabel"><?= $contactButton->button_title ?></h4>
        </div>
        <div class="modal-body">
            <?= $form->field($model, 'title'); ?>
            <?= $form->field($model, 'message')->widget(
                ProsemirrorRichTextEditor::class, [
                'menuClass' => 'plainMenu',
                'placeholder' => Yii::t('MailModule.base', 'Write a message...'),
                'pluginOptions' => ['maxHeight' => '300px'],
            ])->label(false) ?>
            <?= $form->field($model, 'recipient')->widget(UserPickerField::class,
                [
                    'url' => Url::toRoute(['/mail/mail/search-user']),
                    'placeholder' => Yii::t('MailModule.views_mail_create', 'Add recipients'),
                    'focus' => true
                ]
            ); ?>
        </div>
        <div class="modal-footer">

            <?= ModalButton::submitModal(Url::to(['/mail/mail/create']), Yii::t('MailModule.views_mail_create', 'Send')) ?>
            <?= ModalButton::cancel() ?>

        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>


<?= Html::script(' $(\'#recipient\').focus();') ?>

<script>
    $(document).ready(function () {
        if ($('#createmessage-recipient').length > 0) {
            document.getElementById("createmessage-recipient").readOnly = true;
            document.getElementById("createmessage-recipient").parentElement.style.visibility = "hidden";
        }
    });

</script>
