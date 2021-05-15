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

<?= contactButtonWidget::widget(['funding' => $funding, 'contactButton' => $contactButton,'model'=>$model]) ?>


