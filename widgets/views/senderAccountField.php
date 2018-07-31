<?php

use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
?>

<div class="form-group">
    <label class="control-label"><?= Yii::t('XcoinModule.base', 'Sender account'); ?></label>
    <div class="form-control" style="padding-top:4px;">
        <?php if ($senderAccount->space !== null): ?>
            <?= SpaceImage::widget(['space' => $senderAccount->space, 'width' => 24]); ?>
        <?php endif; ?>
        <?php if ($senderAccount->user !== null): ?>
            <?= UserImage::widget(['user' => $senderAccount->user, 'width' => 24]); ?>
        <?php endif; ?>
        <?= Html::encode($senderAccount->title); ?>
        <?= Html::a(Yii::t('XcoinModule.base', 'Change'), $backRoute, ['class' => 'btn btn-sm btn-default pull-right', 'data-target' => '#globalModal', 'data-ui-loader' => '']); ?>
    </div>
</div>
