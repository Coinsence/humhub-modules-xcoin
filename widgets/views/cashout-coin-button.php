<?php

use humhub\libs\Html;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\xcoin\models\Asset;

/** @var Asset $cashOutAsset */
/** @var string $cashOutAssetName */
/** @var ContentContainer $contentContainer */
/** @var array $style */
?>

<?= Html::a('<i class="fa fa-credit-card" aria-hidden="true"></i> ' . Yii::t('XcoinModule.account', 'Cash out') . ' ' . $cashOutAssetName, [
    '/xcoin/transaction/select-account',
    'container' => $contentContainer,
    'assetId' => $cashOutAsset->id
], ['class' => 'btn btn-primary', 'data-target' => '#globalModal', 'style' => $style]) ?>
