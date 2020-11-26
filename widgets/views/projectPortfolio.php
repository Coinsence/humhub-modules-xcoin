<?php

use humhub\assets\Select2BootstrapAsset;
use humhub\modules\space\widgets\Image as SpaceImage;


// test begin
use humhub\modules\user\widgets\ProfileHeader;
use humhub\modules\user\widgets\ProjectPortfolio;
use humhub\modules\user\widgets\ProfileMenu;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Challenge;
use humhub\widgets\FooterMenu;
use yii\bootstrap\Progress;
use \humhub\modules\xcoin\models\Funding;
use \yii\helpers\Html;
use \yii\helpers\Url;
use humhub\modules\xcoin\models\Product;

Assets::register($this);
Select2BootstrapAsset::register($this);
/** @var $selectedChallenge Challenge | null */
/** @var $fundings Funding[] */
/** @var $assetsList array */
/** @var $challengesList array */
/** @var $countriesList array */
/** @var $challengesCarousel array */
/** @var $isProfileOwner array */
/** @var $myActivity array */



?>

<div class="content">
    <div class="projectsPortfolio">
        <!-- <div class="row header">
                <?php /*if ($selectedChallenge):*/ ?>
                <div class="col-md-12">
                    <a class="challenge-url"
                        href="<?/*=$selectedChallenge->space->createUrl('/xcoin/challenge/overview', ['challengeId' => $selectedChallenge->id])*/?>"><?/*=$selectedChallenge->title*/?></a>
                </div>
                <?/*php endif;*/?>
                <div class="col-md-6">
                    <span
                        class="num-projects"><?/*=count($fundings) . ' ' . Yii::t('XcoinModule.funding', 'Project(s)')*/?></span>
                </div>
            </div> -->
        <div class="headerProjects">
            <h2>Projects Portfolio</h2>
            <div class="arrows"></div>
        </div>
        <div class="panels projectsSlider">
            <!-- <div class="col-sm-6 col-md-4 col-lg-3"> -->
            <?php if($isProfileOwner): ?>
            <div class="projectCard createNewProject">
                <a class="add-project" href="<?=Url::to(['/xcoin/funding-overview/new'])?>" data-target="#globalModal">
                    <span class="addProjectCross">
                        <i class="fa fa-plus"></i>
                    </span>
                    <span class="addProjectText"><?=Yii::t('XcoinModule.funding', 'Create Your Project!')?></span>
                </a>
            </div>
            <?php endif?>
            <?php foreach ($fundings as $funding): ?>
            <?php if ($funding->getRemainingDays() > 0): ?>
            <?php
$space = $funding->getSpace()->one();
$cover = $funding->getCover();
?>
            <a href="<?=$space->createUrl('/xcoin/funding/overview', [
    'fundingId' => $funding->id,
]);?>">
                <!-- <div class="col-sm-6 col-md-4 col-lg-3"> -->
                <!-- begin test -->
                <div class="projectCard">
                    <div class="projectCardHeader">
                        <!-- <img src="themes/Coinsence/img/project.jpg" alt="" /> -->

                        <!-- campaign cover start -->
                        <?php if ($cover): ?>
                        <div class="bg" style="background-image: url('<?=$cover->getUrl()?>')"></div>
                        <?=Html::img($cover->getUrl(), ['height' => '140'])?>
                        <?php else: ?>
                        <div class="bg"
                            style="background-image: url('<?=Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png'?>')">
                        </div>
                        <?=Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png', [
    'height' => '140',
])?>
                        <?php endif?>
                        <!-- campaign cover end -->


                    </div>
                    <div class="projectCardBody">
                        <!-- space image start -->
                        <?=SpaceImage::widget([
    'space' => $space,
    'width' => 34,
    'showTooltip' => true,
    'link' => false,
]);?>
                        <!-- space image end -->
                        <!-- <img class="projectImage" src="./img/projectLogo.png" alt="" /> -->
                        <!-- Yii::t('XcoinModule.funding', 'Project by') -->
                        <h2 class="projectName">
                            <!-- Coinsence Tunisia Community Building -->
                            <?=Html::encode($space->name);?>
                            <!-- $funding->title -->
                            <br>
                            <?=Html::encode($funding->title);?>
                            <?php if ($funding->review_status == Funding::FUNDING_NOT_REVIEWED): ?>
                            <div style="color: orange; display: inline">
                                <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip"
                                    title="<?=Yii::t('XcoinModule.funding', 'Under review')?>"></i>
                            </div>
                            <?php else: ?>
                            <div style="color: dodgerblue; display: inline">
                                <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip"
                                    title="<?=Yii::t('XcoinModule.funding', 'Verified')?>"></i>
                            </div>
                            <?php endif;?>
                        </h2>
                        <p class="description">

                            <?=Html::encode($funding->shortenDescription());?>
                        </p>
                        <div class="accumulation">
                            <!-- <span class="accumulationProgression">
        <span class="accumulated"></span>
      </span> -->
                            <!-- campaign raised start -->
                            <?php echo Progress::widget([
    'percent' => $funding->getRaisedPercentage(),
]); ?>

                            <span class="accumulatedTokens"><?=$funding->getRaisedAmount()?></span>
                            <span class="accumulatedPercentage">(<?=$funding->getRaisedPercentage()?>%)</span>
                            <span class="separator"> | </span>
                            <!-- campaign remaining days start -->
                            <?php if ($funding->getRemainingDays() > 2): ?>
                            <i class="fas fa-clock remainingDaysClock"></i>
                            <?php else: ?>
                            <i class="fas fa-clock remainingDaysClockRed"></i>
                            <?php endif;?>
                            <span class="timeLeft"><?=$funding->getRemainingDays()?></span>
                            <?=$funding->getRemainingDays() > 1 ? Yii::t('XcoinModule.funding', 'Days left') : Yii::t('XcoinModule.funding', 'Day left')?>

                            <!-- campaign remaining days end -->
                            <!-- <span class="timeLeft">60 days left</span> -->
                        </div>
                    </div>
                    <div class="projectCardFooter">

                        <!-- campaign requesting start -->
                        <span class="requesting">
                            <?=Yii::t('XcoinModule.funding', 'Requesting:')?> </span>
                        <b class="amount"><?=$funding->getRequestedAmount()?></b>
                        <?=SpaceImage::widget([
    'space' => $funding->getChallenge()->one()->asset->space,
    'width' => 16,
    'showTooltip' => true,
    'link' => false,
    'class' => "coinImage",
]);?>
                        <!-- campaign requesting end -->


                        <!-- <span class="requesting">Requesting : </span>
    <b class="amount">10000</b>
    <img class="coinImage" src="./img/coinsenceToken.jpg" alt="coin" /> -->
                    </div>
                </div>
                <!-- end test -->
            </a>
            <?php endif;?>
            <?php endforeach;?>

        </div>
    </div>
    

</div>