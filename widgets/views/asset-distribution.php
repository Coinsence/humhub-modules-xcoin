<?php

use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
?>
<div>

    <?php if (count($distributions) === 0): ?>
        <?= Yii::t('XcoinModule.base', 'No assets issued yet.'); ?>
    <?php endif; ?>

    <?php foreach ($distributions as $info): ?>
        <?php
        $total = round($info['balance'], 4) . ' (' . Yii::t('XcoinModule.base', 'Shareholdings:') . ' ' . $info['percent'] . '%)';
        ?>

        <?php if ($info['record'] instanceof Space): ?>
            <?= SpaceImage::widget(['space' => $info['record'], 'htmlOptions' => ['style' => 'vertical-align:top;'], 'link' => true, 'showTooltip' => true, 'tooltipText' => $info['record']->name . "\n" . $total]) ?>
        <?php else: ?>
            <?= UserImage::widget(['user' => $info['record'], 'showTooltip' => true, 'tooltipText' => $info['record']->displayName . "\n" . $total]) ?>
        <?php endif; ?>

    <?php endforeach; ?>
</div>
<br />

