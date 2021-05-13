<?php

use humhub\modules\xcoin\models\ChallengeContactButton;
use humhub\modules\xcoin\models\Funding;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\modules\xcoin\widgets\ChallengeContactButton as contactButtonWidget;

/** @var array $nextRoute */
/** @var $contactButton ChallengeContactButton */
/** @var $funding Funding */
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.Funding', $contactButton->button_title), 'closable' => false]) ?>
<div class="modal-body">
    <?= contactButtonWidget::widget(['funding' => $funding, 'contactButton' => $contactButton]) ?>

</div>

<div class="modal-footer">
</div>

<?php ModalDialog::end() ?>
