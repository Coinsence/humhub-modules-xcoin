<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Funding;
use yii\bootstrap\Html;
use yii\bootstrap\Progress;

Assets::register($this);

/** @var $challenges Challenge[] */
/** @var $displayedChallenge Challenge */

// if there is no selected challenge display first challenge
$displayedChallenge = $displayedChallenge ?: ($challenges ? $challenges[0] : []);

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                <?= Html::a(Yii::t('XcoinModule.challenge', 'Add chanllenge'), [
                    '/xcoin/challenge/create',
                    'container' => $this->context->contentContainer
                ], ['class' => 'btn btn-gradient-1 btn-sm', 'data-target' => '#globalModal']); ?>
            <?php endif; ?>
        </div>
        <strong><?= Yii::t('XcoinModule.challenge', 'Space Challenges') ?></strong>
    </div>

    <div class="panel-body">
        <p><?= Yii::t('XcoinModule.challenge', 'This is the list of space challenges.') ?></p>

        <?php if (!$displayedChallenge) : ?>
            <br/>
            <p class="alert alert-warning">
                <?= Yii::t('XcoinModule.challenge', 'Currently there are no challenges.') ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <!-- Scrollable nav bar for challenges ! Remove this comment when implemented -->
            <ul>
                <?php foreach ($challenges as $challenge): ?>
                    <li>
                        <a href="<?= $challenge->getSpace()->one()->createUrl('/xcoin/challenge/index', ['container' => $this->context->contentContainer, 'challengeId' => $challenge->id]); ?>">
                            <?= Html::encode($challenge->title); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <?php if($displayedChallenge): ?>
    <div class="col-md-12 fundingPanel">
        <div class="row">
            <?php
            $space = $displayedChallenge->getSpace()->one();
            $cover = $displayedChallenge->getCover();
            ?>

            <div class="col-md-12">
                <div class="panel cover">
                    <div class="panel-heading">
                        <!-- challenge image start -->
                        <div class="img-container">

                            <?php if ($cover) : ?>
                                <div class="bg" style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                                <?= Html::img($cover->getUrl(), ['width' => '100%']) ?>
                            <?php else : ?>
                                <div class="bg"
                                     style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-challenge-cover.png' ?>')"></div>
                                <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-challenge-cover.png', [
                                    'width' => '100%'
                                ]) ?>
                            <?php endif ?>

                        </div>
                        <!-- challenge image end -->
                    </div>
                    <div class="panel-body">

                        <!-- challenge title start -->
                        <h4 class="funding-title">
                            <?= Html::encode($displayedChallenge->title); ?>
                        </h4>
                        <!-- challenge title end -->

                        <!-- challenge requested coin start -->
                        <div>
                            <?= SpaceImage::widget([
                                'space' => $displayedChallenge->asset->space,
                                'width' => 16,
                                'showTooltip' => true,
                                'link' => false
                            ]); ?>
                            <?= Html::encode($displayedChallenge->asset->space->name); ?>
                        </div>
                        <!-- challenge title end -->

                        <!-- challenge description start -->
                        <div class="description row">
                            <div class="col-md-12">

                                <h5><?= Html::encode($displayedChallenge->description); ?></h5>
                            </div>
                        </div>
                        <!-- challenge description end -->

                    </div>
                    <div class="panel-footer">
                        <!-- Not working still missing params , will be removed -->
                        <!-- challenge add project action start -->
                        <div class="invest-btn">
                            <?= Html::a(Yii::t('XcoinModule.challenge', 'Add you project'), [
                                '/xcoin/funding/edit',
                                'container' => $this->context->contentContainer
                            ], ['class' => 'btn btn-gradient-1 btn-sm', 'data-target' => '#globalModal']); ?>
                        </div>
                        <!-- challenge add project action end -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><?= Yii::t('XcoinModule.challenge', 'Received Submissions') ?></strong>
            </div>
            <div class="panel-body">
                <?php foreach ($displayedChallenge->getFundings()->all() as $funding): ?>
                    <?php
                    $space = $funding->getSpace()->one();
                    $cover = $funding->getCover();
                    ?>

                    <a href="<?= $space->createUrl('/xcoin/funding/overview', [
                        'container' => $space,
                        'fundingId' => $funding->id
                    ]); ?>">
                        <div class="col-md-3 crowd-funding">
                            <div class="panel">
                                <div class="panel-heading">
                                    <!-- campaign cover start -->
                                    <?php if ($cover) : ?>
                                        <div class="bg" style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                                        <?= Html::img($cover->getUrl(), ['height' => '140']) ?>
                                    <?php else : ?>
                                        <div class="bg"
                                             style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>')"></div>
                                        <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png', [
                                            'height' => '140',
                                            'width' => '320'
                                        ]) ?>
                                    <?php endif ?>
                                    <!-- campaign cover end -->
                                    <div class="project-owner">

                                        <!-- space image start -->
                                        <?= SpaceImage::widget([
                                            'space' => $space,
                                            'width' => 34,
                                            'showTooltip' => true,
                                            'link' => false
                                        ]); ?>
                                        <!-- space image end -->
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <h4 class="funding-title">
                                        <?= Html::encode($funding->title); ?>
                                        <?php if ($funding->review_status == Funding::FUNDING_NOT_REVIEWED) : ?>
                                            <div style="color: orange; display: inline">
                                                <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip"
                                                   title="<?= Yii::t('XcoinModule.funding', 'Under review') ?>"></i>
                                            </div>
                                        <?php else: ?>
                                            <div style="color: dodgerblue; display: inline">
                                                <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip"
                                                   title="<?= Yii::t('XcoinModule.funding', 'Verified') ?>"></i>
                                            </div>
                                        <?php endif; ?>
                                    </h4>
                                    <div class="media">
                                        <div class="media-left media-middle"></div>
                                        <div class="media-body">
                                            <!-- campaign description start -->
                                            <p class="media-heading"><?= Html::encode($funding->shortenDescription()); ?></p>
                                            <!-- campaign description end -->
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <div class="funding-progress">
                                        <div>
                                            <!-- campaign raised start -->
                                            <?= Yii::t('XcoinModule.funding', 'Raised:') ?>
                                            <strong><?= $funding->getRaisedAmount() ?></strong>
                                            (<strong><?= $funding->getRaisedPercentage() ?>%</strong>)
                                            <!-- campaign raised end -->
                                        </div>
                                        <div class="pull-right">
                                            <!-- campaign remaining days start -->
                                            <?php if ($funding->getRemainingDays() > 2) : ?>
                                                <div class="clock"></div>
                                            <?php else : ?>
                                                <div class="clock red"></div>
                                            <?php endif; ?>
                                            <div class="days">
                                                <?php if ($funding->getRemainingDays() > 0) : ?>
                                                    <strong><?= $funding->getRemainingDays() ?></strong> <?= $funding->getRemainingDays() > 1 ? Yii::t('XcoinModule.funding', 'Days left') : Yii::t('XcoinModule.funding', 'Day left') ?>
                                                <?php else : ?>
                                                    <strong><?= Yii::t('XcoinModule.funding', 'Closed') ?></strong>
                                                <?php endif; ?>
                                            </div>
                                            <!-- campaign remaining days end -->
                                        </div>
                                        <!-- campaign raised start -->
                                        <?php echo Progress::widget([
                                            'percent' => $funding->getRaisedPercentage()
                                        ]); ?>
                                    </div>
                                    <div class="funding-details row">
                                        <div class="col-md-12">
                                            <!-- campaign requesting start -->
                                            <span>
                                    <?= Yii::t('XcoinModule.funding', 'Requesting:') ?>
                                    <strong><?= $funding->getRequestedAmount() ?></strong>
                                </span>
                                            <?= SpaceImage::widget([
                                                'space' => $challenge->space,
                                                'width' => 16,
                                                'showTooltip' => true,
                                                'link' => false
                                            ]); ?>
                                            <!-- campaign requesting end -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>