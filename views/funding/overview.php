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
use humhub\libs\Iso3166Codes;
use yii\bootstrap\Progress;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\xcoin\helpers\FundingHelper;

Assets::register($this);

/**
 * @var $funding Funding
 */
/**
 * @var $contactButtons ChallengeContactButtonAlias[]
 */
?>

<div class="space-funding">
    <?php
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
    <div class="panel">
        <div class="panel-heading">
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
                    <?= Html::a('<i class="fa fa-check"></i>' . Yii::t('XcoinModule.funding', 'Close campaign'),
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
                <?php else : ?>
                    <?= Html::a('<i class="fa fa-refresh"></i>' . Yii::t('XcoinModule.funding', 'Restart campaign'),
                        [
                            '/xcoin/funding/restart',
                            'id' => $funding->id,
                            'container' => $this->context->contentContainer
                        ],
                        [
                            'class' => 'edit-btn',
                            'style' => 'top: 60px; color:orange',
                            'title' => 'Accept investment'
                        ]
                    ) ?>
                <?php endif; ?>

                <?= Html::a('<i class="fa fa-times"></i>' . Yii::t('XcoinModule.funding', 'Cancel campaign'),
                    [
                        '/xcoin/funding/cancel',
                        'id' => $funding->id,
                        'container' => $this->context->contentContainer
                    ],
                    [
                        'class' => 'edit-btn',
                        'style' => 'top: 103px; color:red',
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
        <div class="panel-body">
            <!-- campaign title start -->
            <h4 class="funding-title">
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
                </small>
                <!-- campaign review status end -->
            </h4>
            <!-- campaign title end -->


            <!-- campaign requesting start -->
            <h6 class="value">
                <?= Yii::t('XcoinModule.funding', 'Requesting:') ?>
                <strong><?= $funding->getRequestedAmount() ?></strong>
                <?= SpaceImage::widget([
                    'space' => $funding->getChallenge()->one()->asset->space,
                    'width' => 24,
                    'showTooltip' => true,
                    'link' => true
                ]); ?>
            </h6>
            <!-- campaign requesting end -->

            <!-- campaign location start -->
            <h6 class="location">
                <?= Yii::t('XcoinModule.funding', 'Location:') ?>
                <strong><?= Iso3166Codes::country($funding->country) . ', ' . $funding->city ?></strong>
            </h6>
            <!-- campaign location end -->


            <!-- campaign categories start -->
            <h6 class="categories"><?= Yii::t('XcoinModule.funding', 'Categories:') ?></h6>
            <ul>
                <?php foreach ($funding->getCategories()->all() as $category) : ?>
                    <li>
                        <?= $category->name; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- campaign categories end -->

            <!-- campaign description start -->
            <div class="description row">
                <div class="col-md-12">
                    <p class="media-heading"><?= Html::encode($funding->description); ?></p>
                </div>
            </div>
            <!-- campaign description end -->


            <div class="progress-info">
                <!-- campaign raised start -->
                <div class="raised">
                    <i class="fa fa-dot-circle-o"></i>
                    <div class="infos">
                        <strong><?= $funding->getRaisedAmount() ?></strong>
                        (<strong><?= $funding->getRaisedPercentage() ?>%</strong>)
                        <br>
                        <h6><?= Yii::t('XcoinModule.funding', 'Raised:') ?></h6>
                    </div>
                </div>
                <!-- campaign raised end -->
                <!-- campaign remaining days start -->
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
                <!-- campaign remaining days end -->
            </div>

            <div class="funding-progress">
                <!-- campaign raised start -->
                <?php echo Progress::widget([
                    'percent' => $funding->getRaisedPercentage(),
                ]); ?>
                <!-- campaign raised end -->
            </div>

            <!-- campaign content start -->
            <?= RichText::output($funding->content); ?>
            <!-- campaign content end -->

            <?php if (!empty($funding->youtube_link)): ?>
                <div class="youtube-video">
                    <iframe id="player" type="text/html" width="640" height="390" src="<?= FundingHelper::getYoutubeEmbedUrl($funding->youtube_link) ?>" frameborder="0"></iframe>
                </div>
            <?php endif; ?>

            <?php foreach ($contactButtons as $contactButton): ?>
                <?php if (Yii::$app->user->isGuest): ?>
                    <div>
                        <?= Html::a(Yii::t('XcoinModule.funding', $contactButton->button_title), Yii::$app->user->loginUrl, ['data-target' => '#globalModal']) ?>
                    </div>
                <?php else: ?>
                    <div>
                        <?= Html::a(Yii::t('XcoinModule.funding', $contactButton->button_title), [
                            'contact',
                            'fundingId' => $funding->id,
                            'contactButtonId' => $contactButton->id,
                            'container' => $funding->getSpace()->one(),
                        ], ['data-target' => '#globalModal']); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>
        <div class="panel-footer">

            <!-- campaign invest action start -->
            <?php if (!$funding->canInvest()): ?>
            <div class="invest-btn disabled">
                <?php else: ?>
                <div class="invest-btn">
                    <?php endif; ?>
                    <?php if (Yii::$app->user->isGuest): ?>
                        <?= Html::a(Yii::t('XcoinModule.funding', 'Fund this project'), Yii::$app->user->loginUrl, ['data-target' => '#globalModal']) ?>
                    <?php else: ?>
                        <?php if ($funding->status !== Funding::FUNDING_STATUS_INVESTMENT_ACCEPTED): ?>
                            <?= Html::a(Yii::t('XcoinModule.funding', 'Fund this project'), [
                                'invest',
                                'fundingId' => $funding->id,
                                'container' => $this->context->contentContainer
                            ], ['data-target' => '#globalModal']); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <!-- campaign invest action end -->
            </div>
        </div>
    </div>
</div>
