<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\ChallengeContactButton as ChallengeContactButtonAlias;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\widgets\ChallengeImage;
use yii\bootstrap\Carousel;
use humhub\libs\Html;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\libs\Iso3166Codes;
use yii\bootstrap\Progress;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\xcoin\helpers\FundingHelper;
use humhub\modules\xcoin\models\Asset;

Assets::register($this);

/**
 * @var $funding Funding
 */
/**
 * @var $contactButtons ChallengeContactButtonAlias[]
 */
?>

<div class="co-overview">
    <?php
    $space = $funding->getSpace()->one();
    $challenge = $funding->getChallenge()->one();
    $cover = $funding->getCover();
    $gallery = $funding->getGallery();

    $carouselItems = [];

    $coverItemUrl = '';

    if ($cover):
        $coverItemUrl = $cover->getUrl();
    else:
        $coverItemUrl = Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png';
    endif;

    $coverItem = "<div class=\"carousel-item\">";
    $coverItem .= "<div class=\"bg\" style=\"background-image: url('{$coverItemUrl}')\"></div>";
    $coverItem .= Html::img($coverItemUrl, ['width' => '100%']);
    $coverItem .= "</div>";

    $carouselItems[] = $coverItem;

    foreach ($gallery as $item):

        $carouselItem = "<div class=\"carousel-item\">";
        $carouselItem .= "<div class=\"bg\" style=\"background-image: url('{$item->getUrl()}')\"></div>";
        $carouselItem .= Html::img($item->getUrl(), ['width' => '100%']);
        $carouselItem .= "</div>";

        $carouselItems[] = $carouselItem;
    endforeach;
    ?>
    <div class="co-overview-heading">
        <!-- campaign title start -->
        <h4 class="co-overview-title">
            <?= Html::encode($funding->title); ?>
            <!-- campaign review status start -->
            <small>
                <?php if ($funding->status == Funding::FUNDING_STATUS_INVESTMENT_ACCEPTED) : ?>
                    <div style="color: green; display: inline">
                        ( <i class="fa fa-check-circle-o"
                                aria-hidden="true"></i> <?= Yii::t('XcoinModule.funding', 'Investment Accepted') ?>
                        )
                    </div>
                <?php endif; ?>
                <?php if ($funding->status == Funding::FUNDING_STATUS_INVESTMENT_RESTARTED) : ?>
                    <div style="color: orange; display: inline">
                        ( <i class="fa fa-refresh"
                                aria-hidden="true"></i> <?= Yii::t('XcoinModule.funding', 'Investment Restarted') ?>
                        )
                    </div>
                <?php endif; ?>
                <?php if ($funding->review_status == Funding::FUNDING_NOT_REVIEWED) : ?>
                    <div style="color: orange; display: inline">
                        ( <i class="fa fa-check-circle-o"
                                aria-hidden="true"></i> <?= Yii::t('XcoinModule.funding', 'Under review') ?>
                        )
                    </div>
                <?php else: ?>
                    <div style="color: dodgerblue; display: inline">
                        ( <i class="fa fa-check-circle-o"
                                aria-hidden="true"></i> <?= Yii::t('XcoinModule.funding', 'Verified') ?>
                        )
                    </div>
                <?php endif; ?>
                <?php if ($funding->published == Funding::FUNDING_HIDDEN) : ?>
                    <div style="color: orange; display: inline">
                        ( <i class="fa fa-check-circle-o"
                                aria-hidden="true"></i> <?= Yii::t('XcoinModule.funding', 'Hidden') ?>
                        )
                    </div>
                <?php else: ?>
                    <div style="color: green; display: inline">
                        ( <i class="fa fa-check-circle-o"
                                aria-hidden="true"></i> <?= Yii::t('XcoinModule.funding', 'Published') ?>
                        )
                    </div>
                <?php endif; ?>
                <?php if ($funding->activate_funding == Funding::FUNDING_DEACTIVATED) : ?>
                    <div style="color: orange; display: inline">
                        ( <i class="fa fa-check-circle-o"
                                aria-hidden="true"></i> <?= Yii::t('XcoinModule.funding', 'Deactivated') ?>
                        )
                    </div>
                <?php else: ?>
                    <div style="color: green; display: inline">
                        ( <i class="fa fa-check-circle-o"
                                aria-hidden="true"></i> <?= Yii::t('XcoinModule.funding', 'Activated') ?>
                        )
                    </div>
                <?php endif; ?>
            </small>
            <!-- campaign review status end -->
        </h4>
        <!-- campaign title end -->

        <div class="co-overview-subtitle">
            <!-- campaign description start -->
            <div class="co-overview-description">
                <p class="media-heading"><?= Html::encode($funding->description); ?></p>
            </div>
            <!-- campaign description end -->

            <div class="co-overview-social">
                <p><?= Yii::t('XcoinModule.funding', 'Partager') ?></p>
            </div>
        </div>
    </div>
    <div class="panel panel-default panel-head">
        <!-- campaign cover start -->
        <div class="img-container">
            <?php if ($cover) : ?>
                <?php if (count($carouselItems) > 1): ?>
                    <?= Carousel::widget([
                        'items' => $carouselItems,
                    ]) ?>
                <?php else: ?>
                    <div class="bg"
                        style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                    <?= Html::img($cover->getUrl(), [
                        'width' => '100%'
                    ]) ?>
                <?php endif; ?>
            <?php else : ?>
                <div class="bg"
                    style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>')"></div>
                <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png', [
                    'width' => '100%'
                ]) ?>
            <?php endif ?>
        </div>
        <!-- campaign cover end -->

        <!-- challenge image start -->
        <?= ChallengeImage::widget(['challenge' => $funding->getChallenge()->one(), 'width' => 30, 'link' => true, 'linkOptions' => ['class' => 'challenge-btn']]) ?>
        <!-- challenge image end -->

        <!-- campaign buttons start -->
        <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
            <?= Html::a(
                '<i class="fa fa-pencil"></i>' . Yii::t('XcoinModule.funding', 'Edit'),
                [
                    '/xcoin/funding/edit',
                    'id' => $funding->id,
                    'container' => $this->context->contentContainer
                ],
                [
                    'data-target' => '#globalModal',
                    'class' => 'edit-btn',
                    'title' => 'Edit campaign details'
                ]
            ) ?>

            <?php if ($funding->status != Funding::FUNDING_STATUS_INVESTMENT_ACCEPTED) : ?>
                <?= Html::a('<i class="fa fa-check"></i>' . Yii::t('XcoinModule.funding', 'Close funding and enable transaction from funding account'),
                    [
                        '/xcoin/funding/accept',
                        'id' => $funding->id,
                        'container' => $this->context->contentContainer
                    ],
                    [
                        'class' => 'edit-btn',
                        'style' => 'top: 60px; color:green',
                        'title' => 'Accept investment'
                    ]
                ) ?>
            <?php endif; ?>

            <?php if ($funding->published == Funding::FUNDING_PUBLISHED) : ?>
                <?= Html::a('<i class="fa fa-times"></i>' . Yii::t('XcoinModule.funding', 'Hide campaign'),
                    [
                        '/xcoin/funding/publish',
                        'id' => $funding->id,
                        'container' => $this->context->contentContainer
                    ],
                    [
                        'class' => 'edit-btn',
                        'style' => 'top: 103px; color:red',
                        'title' => 'Hide campaign'
                    ]
                ) ?>
            <?php else : ?>
                <?= Html::a('<i class="fa fa-check"></i>' . Yii::t('XcoinModule.funding', 'Publish campaign'),
                    [
                        '/xcoin/funding/publish',
                        'id' => $funding->id,
                        'container' => $this->context->contentContainer
                    ],
                    [
                        'class' => 'edit-btn',
                        'style' => 'top: 103px; color:green',
                        'title' => 'Publish campaign'
                    ]
                ); ?>
            <?php endif; ?>
            <?php if ($funding->activate_funding == Funding::FUNDING_ACTIVATED) : ?>
                <?= Html::a('<i class="fa fa-times"></i>' . Yii::t('XcoinModule.funding', 'Deactivate Funding'),
                    [
                        '/xcoin/funding/show',
                        'id' => $funding->id,
                        'container' => $this->context->contentContainer
                    ],
                    [
                        'class' => 'edit-btn',
                        'style' => 'top: 146px; color:red',
                        'title' => 'Deactivate Funding'
                    ]
                ) ?>
            <?php else : ?>
                <?= Html::a('<i class="fa fa-check"></i>' . Yii::t('XcoinModule.funding', 'Activate Funding'),
                    [
                        '/xcoin/funding/show',
                        'id' => $funding->id,
                        'container' => $this->context->contentContainer
                    ],
                    [
                        'class' => 'edit-btn',
                        'style' => 'top: 146px; color:green',
                        'title' => 'Activate Funding'
                    ]
                ); ?>
            <?php endif; ?>
            <?= Html::a('<i class="fa fa-times"></i>' . Yii::t('XcoinModule.funding', 'Cancel campaign'),
                [
                    '/xcoin/funding/cancel',
                    'id' => $funding->id,
                    'container' => $this->context->contentContainer
                ],
                [
                    'class' => 'edit-btn',
                    'style' => 'top: 189px; color:red',
                    'title' => 'Delete campaign'
                ]
            ); ?>
        <?php endif; ?>
        <!-- campaign buttons end -->

        <!-- campaign review button start -->
        <?php if (SpaceHelper::canReviewProject($funding->challenge->space) || PublicOffersHelper::canReviewSubmittedProjects()): ?>
            <?php if ($funding->review_status == Funding::FUNDING_NOT_REVIEWED) : ?>
                <?= Html::a('<i class="fa fa-check"></i> ' . Yii::t('XcoinModule.funding', 'Trusted'), ['/xcoin/funding/review', 'id' => $funding->id, 'status' => Funding::FUNDING_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-trusted pull-right']) ?>
            <?php else : ?>
                <?= Html::a('<i class="fa fa-close"></i> ' . Yii::t('XcoinModule.funding', 'Untrusted'), ['/xcoin/funding/review', 'id' => $funding->id, 'status' => Funding::FUNDING_NOT_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-untrusted pull-right']) ?>
            <?php endif; ?>
        <?php endif; ?>
        <!-- campaign review button end -->
    </div>
    <div class="panel panel-default panel-body">
        <div class="co-overview-container row">
            <div class="text col-md-8">
                <h4 class="title">
                    <?= Yii::t('XcoinModule.funding', 'About the project') ?>
                </h4>
                <!-- campaign content start -->
                <div class="co-overview-content">
                    <?= RichText::output($funding->content); ?>
                </div>
                <!-- campaign content end -->

                <?php if (!empty($funding->youtube_link)): ?>
                    <div class="youtube-video">
                        <iframe id="player" type="text/html" width="640" height="390" src="<?= FundingHelper::getYoutubeEmbedUrl($funding->youtube_link) ?>" frameborder="0"></iframe>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <div class="side-widget">
                    <div class="info">
                        <label><?= Yii::t('XcoinModule.funding', 'Created by') ?></label>
                        <span><strong><?= $space->name ?></strong></span>

                        <?php if ($funding->canInvest()) : ?>
                            <label><?= Yii::t('XcoinModule.funding', 'Requesting') ?></label>
                            <span class="requesting">
                                <strong>
                                    <?= $funding->getRequestedAmount() ?>
                                </strong>
                                <?= SpaceImage::widget([
                                    'space' => $challenge->asset->space,
                                    'width' => 24,
                                    'showTooltip' => true,
                                    'link' => true
                                ]); ?>
                            </span>
                        <?php endif; ?>

                        <label><?= Yii::t('XcoinModule.funding', 'Rewarding') ?></label>
                        <span>
                            <?php if ($challenge->acceptAnyRewardingAsset()): ?>
                                <?php if ($funding->canInvest()) : ?>
                                    <?= Yii::t('XcoinModule.funding', 'Per invested coin') ?>
                                    <strong><?= $funding->exchange_rate ?></strong>
                                <?php endif; ?>
                                <?= SpaceImage::widget([
                                    'space' => $space,
                                    'width' => 24,
                                    'showTooltip' => true,
                                    'link' => true
                                ]); ?>
                            <?php elseif ($challenge->acceptSpecificRewardingAsset()): ?>
                                <?php if ($funding->canInvest()) : ?>
                                    <?= Yii::t('XcoinModule.funding', 'Per invested coin') ?>
                                    <strong><?= $challenge->exchange_rate ?></strong>
                                <?php endif; ?>
                                <?= SpaceImage::widget([
                                    'space' => Asset::findOne(['id' => $challenge->specific_reward_asset_id])->getSpace()->one(),
                                    'width' => 24,
                                    'showTooltip' => true,
                                    'link' => true
                                ]); ?>
                            <?php else: ?>
                                <?= Yii::t('XcoinModule.funding', 'no COIN rewarding') ?>
                            <?php endif; ?>
                        </span>

                        <label><?= Yii::t('XcoinModule.funding', 'Location') ?></label>
                        <span><strong><?= Iso3166Codes::country($funding->country) . ', ' . $funding->city ?></strong></span>

                        <label><?= Yii::t('XcoinModule.funding', 'Category') ?></label>
                        <span>
                            <?= join(', ', array_map(function ($cat) {
                                return '<strong>' . $cat->name . '</strong>';
                            }, $funding->getCategories()->all())) ?> 
                        </span>
                    </div>
                    <?php if ($funding->canInvest()) : ?>
                        <hr/>
                        <div class="funding-progress">
                            <div class="progress-info">
                                <div class="raised">
                                    <i class="fa fa-dot-circle-o"></i>
                                    <div class="infos">
                                        <strong><?= $funding->getRaisedAmount() ?></strong>
                                        (<strong><?= $funding->getRaisedPercentage() ?>%</strong>)
                                        <br>
                                        <h6><?= Yii::t('XcoinModule.funding', 'Raised:') ?></h6>
                                    </div>
                                </div>
                                <div class="days">
                                    <i class="fa fa-clock-o"></i>
                                    <div class="infos">
                                        <?php if ($funding->getRemainingDays() > 0) : ?>
                                            <strong><?= $funding->getRemainingDays() ?></strong>
                                            <br>
                                            <h6><?= $funding->getRemainingDays() > 1 ? Yii::t('XcoinModule.funding', 'Days left') : Yii::t('XcoinModule.funding', 'Day left') ?></h6>
                                        <?php else : ?>
                                            <strong><?= Yii::t('XcoinModule.funding', 'Closed') ?></strong>
                                            <br>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="progress-value">
                                <?php echo Progress::widget([
                                    'percent' => $funding->getRaisedPercentage(),
                                ]); ?>
                            </div>
                        </div>
                        <hr/>
                        <div class="contributors">
                            <h6><?= Yii::t('XcoinModule.funding', 'Contributors') ?></h6>
                            <?php $distributions = $funding->getContributors(); ?>
                            <?php if (count($distributions) === 0): ?>
                                <?= Yii::t('XcoinModule.funding', 'No contribution done yet.') ?>
                            <?php endif; ?>

                            <?php foreach ($distributions as $info): ?>
                                <?php
                                $total = round($info['balance'], 4) . ' (' . Yii::t('XcoinModule.base', 'Shareholdings:') . ' ' . $info['percent'] . '%)';
                                ?>
                                <?= UserImage::widget(['user' => $info['record'], 'showTooltip' => true, 'tooltipText' => $info['record']->displayName . "\n" . $total]) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <hr/>
                    <div class="actions">
                        <?php foreach ($contactButtons as $contactButton): ?>
                            <?php if ($contactButton->isButtonEnabled()) : ?>
                                <?php if (Yii::$app->user->isGuest): ?>
                                    <div class="custom-btn">
                                        <?= Html::a(Yii::t('XcoinModule.funding', $contactButton->button_title), Yii::$app->user->loginUrl, ['data-target' => '#globalModal']) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="custom-btn">
                                        <?= Html::a(Yii::t('XcoinModule.funding', $contactButton->button_title), [
                                            'contact',
                                            'fundingId' => $funding->id,
                                            'contactButtonId' => $contactButton->id,
                                            'container' => $funding->getSpace()->one(),
                                        ], ['data-target' => '#globalModal']); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <!-- campaign invest action start -->
                        <?php if ($funding->canInvest() && !$funding->challenge->isClosed()) : ?>
                            <div class="invest-btn">
                                <?php if (Yii::$app->user->isGuest): ?>
                                    <?= Html::a(Yii::t('XcoinModule.funding', 'Fund this project'), Yii::$app->user->loginUrl, ['data-target' => '#globalModal']) ?>
                                <?php else: ?>
                                    <?php if ($funding->activate_funding !== Funding::FUNDING_DEACTIVATED): ?>
                                        <?= Html::a(Yii::t('XcoinModule.funding', 'Fund this project'), [
                                            'invest',
                                            'fundingId' => $funding->id,
                                            'container' => $funding->getSpace()->one()
                                        ], ['data-target' => '#globalModal']); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <!-- campaign invest action end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
