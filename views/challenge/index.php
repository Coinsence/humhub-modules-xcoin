<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\helpers\AssetHelper;
use yii\helpers\Url;
use yii\helpers\Html;


Assets::register($this);

/** @var $challenges Challenge[] */

?>

<div class="space-challenges">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><?= Yii::t('XcoinModule.challenge', 'Space Challenges') ?></strong>
        </div>
        <div class="panel-body">
            <div class="panels">
                <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">

                        <a class="add-challenge" href="<?= Url::to(['/xcoin/challenge/create', 'container' => $this->context->contentContainer]) ?>" data-target="#globalModal">
                            <span class="icon">
                                <i class="cross"></i>
                            </span>
                            <span class="text"><?= Yii::t('XcoinModule.challenge', 'Add a challenge!') ?></span>
                        </a>
                    </div>
                <?php endif; ?>
                <?php foreach ($challenges as $challenge): ?>
                    <?php
                    $space = $challenge->getSpace()->one();
                    $cover = $challenge->getCover();
                    ?>
                    <a href="<?= $space->createUrl('/xcoin/challenge/overview', [
                        'challengeId' => $challenge->id
                    ]); ?>">
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="panel">
                                <div class="panel-heading">
                                    <!-- challenge image start -->
                                    <?php if ($cover) : ?>
                                        <div class="bg" style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                                        <?= Html::img($cover->getUrl(), ['height' => '240']) ?>
                                    <?php else : ?>
                                        <div class="bg"
                                             style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-challenge-cover.png' ?>')"></div>
                                        <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-challenge-cover.png', [
                                            'height' => '240'
                                        ]) ?>
                                    <?php endif ?>
                                    <!-- challenge image end -->
                                </div>
                                <div class="panel-body">
                                    <!-- challenge title start -->
                                    <h5 class="challenge-title">
                                        <?= Html::encode($challenge->title); ?>
                                    </h5>
                                    <!-- challenge title end -->
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
