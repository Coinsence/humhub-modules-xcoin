<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2022 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Projectplace;
use humhub\modules\xcoin\widgets\SocialShare;
use yii\helpers\Html;

Assets::register($this);

/** @var $projectplace Projectplace */

/** @var $space Space */
$space = $projectplace->space;

?>

<div class="cs-overview">
    <?= Html::a('<i class="fa fa-long-arrow-left"></i>', ['/xcoin/projectplace', 'container' => $space], ['class' => 'close-cs-overview']); ?>
    <div class="panel panel-default panel-head">
        <div class="cs-overview-info">
            <h2 class="cs-overview-title"><?= $projectplace->title ?></h2>
            <div class="cs-overview-asset">
                <?php if (null !== $projectplace->investAsset) : ?>
                    <div>
                        <!-- projectplace invest asset start -->
                        <span class="asset-name"><?= Yii::t('XcoinModule.Projectplace', 'Invest Coin') ?></span>
                        <?= SpaceImage::widget([
                            'space' => $projectplace->investAsset->space,
                            'width' => 26,
                            'showTooltip' => true,
                            'link' => true
                        ]); ?>
                        <!-- projectplace invest end -->
                    </div>
                <?php endif; ?>
                <?php if (null !== $projectplace->rewardAsset) : ?>
                    <div style="margin-top: 10px">
                        <!-- projectplace reward asset start -->
                        <span class="asset-name"><?= Yii::t('XcoinModule.Projectplace', 'Reward Coin') ?></span>
                        <?= SpaceImage::widget([
                            'space' => $projectplace->rewardAsset->space,
                            'width' => 26,
                            'showTooltip' => true,
                            'link' => true
                        ]); ?>
                        <!-- projectplace reward end -->
                    </div>
                <?php endif; ?>
            </div>
            <div class="cs-overview-description"><?= RichText::output($projectplace->description); ?></div>
        </div>

        <div class="img-container">
            <!-- projectplace image start -->
            <div class="bg" style="background-image: url('<?= $projectplace->getCover()->getUrl() ?>')"></div>
            <?= Html::img($projectplace->getCover()->getUrl(), ['height' => '530']) ?>
            <!-- projectplace image end -->

            <!-- projectplace edit button start -->
            <?php if (AssetHelper::canManageAssets($space)): ?>
                <?= Html::a(
                    '<i class="fa fa-pencil"></i>' . Yii::t('XcoinModule.Projectplace', 'Edit'),
                    ['/xcoin/projectplace/form', 'projectplaceId' => $projectplace->id, 'container' => $space],
                    ['data-target' => '#globalModal', 'class' => 'edit-btn']
                ) ?>
            <?php endif; ?>
            <!-- projectplace edit button end -->

            <?= SocialShare::widget(['url' => Yii::$app->request->hostInfo . Yii::$app->request->url]); ?>
        </div>
    </div>
</div>

