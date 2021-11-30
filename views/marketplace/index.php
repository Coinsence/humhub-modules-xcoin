<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Marketplace;
use yii\helpers\Url;
use yii\helpers\Html;

Assets::register($this);

/** @var $marketplaces Marketplace[] */
/** @var $userGuide boolean */

?>

<div class="space-challenges">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><?= Yii::t('XcoinModule.marketplace', 'Space Marketplaces') ?></strong>
        </div>
        <div class="panel-body">
            <div class="panels">
                <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <a class="add-challenge"
                           href="<?= Url::to(['/xcoin/marketplace/create', 'container' => $this->context->contentContainer]) ?>"
                           data-target="#globalModal">
                            <span class="icon">
                                <i class="cross"></i>
                            </span>
                            <span class="text"><?= Yii::t('XcoinModule.marketplace', 'Add a marketplace!') ?></span>
                        </a>
                    </div>
                    <?php if ($userGuide) : ?>
                        <div class="col-md-12">
                            <div class="s2_streamContent" data-stream-content="">
                                <div class="streamMessage placeholder-empty-stream">
                                    <div class="panel">
                                        <a href="<?= Url::to(['/xcoin/marketplace/create', 'container' => $this->context->contentContainer]) ?>"
                                           data-target="#globalModal" class="panel-body">
                                            <?= Yii::t('XcoinModule.marketplace', '<b>This marketplace is empty !</b><br>You can Start here by adding a new marketplace') ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (count($marketplaces) == 0): ?>
                        <p class="alert alert-warning col-md-12">
                            <?= Yii::t('XcoinModule.marketplace', 'Currently there are no marketplaces.') ?>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
                <?php foreach ($marketplaces as $marketplace): ?>
                    <?php
                        $space = $marketplace->getSpace()->one();
                        $cover = $marketplace->getCover();
                    ?>
                    <a href="<?= $space->createUrl('/xcoin/marketplace/overview', [
                        'marketplaceId' => $marketplace->id
                    ]); ?>">
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="panel">
                                <div class="panel-heading">
                                    <!-- marketplace image start -->
                                    <?php if ($cover) : ?>
                                        <div class="bg" style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                                        <?= Html::img($cover->getUrl(), ['height' => '240']) ?>
                                    <?php else : ?>
                                        <div class="bg"
                                             style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-marketplace-cover.png' ?>')"></div>
                                        <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-marketplace-cover.png', [
                                            'height' => '240'
                                        ]) ?>
                                    <?php endif ?>
                                    <!-- marketplace image end -->
                                </div>
                                <div class="panel-body">
                                    <!-- marketplace title start -->
                                    <h5 class="challenge-title">
                                        <?= Html::encode($marketplace->title); ?>
                                    </h5>
                                    <!-- marketplace title end -->
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
