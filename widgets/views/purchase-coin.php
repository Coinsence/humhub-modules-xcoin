<?php

use humhub\libs\Html;
use humhub\modules\space\widgets\Image as SpaceImage;

/** @var $style string */
/** @var $contentContainer humhub\modules\content\components\ContentContainerActiveRecord */
/** @var $name string */
/** @var $noCoinsWarning boolean */
/** @var $coinsBlanace number */
/** @var $asset humhub\modules\xcoin\models\Asset */

/** @var $res string */

$res = Yii::$app->request->get('res');

?>
<?php if ($noCoinsWarning): ?>
    <div style="margin-bottom: 18px;">
        <span><?= Yii::t('XcoinModule.overview', 'Your current balance is:') ?></span> <strong><?= $coinsBlanace ?></strong>&nbsp; <?= SpaceImage::widget(['space' => $asset->space, 'width' => 20, 'showTooltip' => true, 'link' => false]) ?>
        <p><?= Yii::t('XcoinModule.overview', 'You can buy and get instantly COINs using the button below') ?></p>
    </div>
<?php endif; ?>
<?= Html::a('<i class="fa fa-money" aria-hidden="true"></i> ' . Yii::t('XcoinModule.overview', 'Buy') . ' ' . $name, [
    '/xcoin/overview/purchase-coin',
    'container' => $contentContainer
], ['class' => 'btn ' . ($coinsBlanace > 0 ? 'btn-default' : 'btn-primary'), 'data-target' => '#globalModal', 'style' => $style]) ?>
<?php if ($res === 'success') : ?>
    <p class="alert alert-info col-md-12">
        <?= Yii::t('XcoinModule.overview', 'Your purchase is being processed.. hold tight, it may take a while') ?>
    </p>
<?php endif; ?>
<?php if ($res === 'error') : ?>
    <p class="alert alert-danger col-md-12">
        <?= Yii::t('XcoinModule.overview', 'Your purchase is not processed, an error has occurred on the bridge side') ?>
    </p>
<?php endif; ?>
