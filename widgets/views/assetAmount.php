<?php

use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
?>

<div class="amount">
    <strong><?= $amount ?></strong>
    <?= SpaceImage::widget(['space' => $space, 'width' => 20, 'link' => true]) ?>
</div>
