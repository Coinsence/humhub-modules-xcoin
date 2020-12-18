<?php

use humhub\libs\Iso3166Codes;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\controllers\NetworkController;
use humhub\modules\xcoin\models\Tag;
use humhub\modules\space\models\Membership as Membership;
use humhub\modules\xcoin\utils\StringUtils;
use kv4nt\owlcarousel\OwlCarouselWidget;
use humhub\assets\Select2BootstrapAsset;
use yii\helpers\Html;
use \yii\helpers\Url;

Assets::register($this);
Select2BootstrapAsset::register($this);

/** @var $results Space[]|User[] */
/** @var $tags Tag[] */
/** @var $type string */
/** @var $allSpacesTag Tag */
/** @var $allUsersTag Tag */

$count = count($results);
?>
<div class="network">
    <div class="container">
        <div class="filters">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    OwlCarouselWidget::begin([
                        'container' => 'div',
                        'containerOptions' => [
                            'class' => 'categories'
                        ],
                        'pluginOptions' => [
                            'responsive' => [
                                0 => [
                                    'items' => 2
                                ],
                                520 => [
                                    'items' => 3
                                ],
                                768 => [
                                    'items' => 4
                                ],
                                1192 => [
                                    'items' => 5
                                ],
                                1366 => [
                                    'items' => 6
                                ],
                                1556 => [
                                    'items' => 8
                                ],
                            ],
                            'margin' => 10,
                            'nav' => true,
                            'dots' => false,
                            'navText' => ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>']
                        ]
                    ]);
                    ?>
                    <label class="tag <?= $allUsersTag ? '': 'all' ?>">
                        <input type="radio" name="category" checked>
                        <a href="<?= Url::to(['/xcoin/network', 'type' => 'user']) ?>">
                            <?php if ($allUsersTag) : ?>
                                <span style="background-image: url('<?= $allUsersTag->getCover() ? $allUsersTag->getCover()->getUrl() : Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-tag-cover.png' ?>'); "></span>
                            <?php else: ?>
                                <span>
                                    <label><?= Yii::t('XcoinModule.network', 'All Users') ?></label>
                                </span>
                            <?php endif; ?>
                        </a>
                    </label>
                    <label class="tag <?= $allSpacesTag ? '' : 'all' ?>">
                        <input type="radio" name="category" checked>
                        <a href="<?= Url::to(['/xcoin/network', 'type' => 'space']) ?>">
                            <?php if ($allSpacesTag) : ?>
                                <span style="background-image: url('<?= $allSpacesTag->getCover() ? $allSpacesTag->getCover()->getUrl() : Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-tag-cover.png' ?>'); "></span>
                            <?php else: ?>
                                <span>
                                    <label><?= Yii::t('XcoinModule.network', 'All Spaces') ?></label>
                                </span>
                            <?php endif; ?>
                        </a>
                    </label>
                    <?php foreach ($tags as $tag): ?>
                        <label class="tag">
                            <a href="<?= Url::to(['/xcoin/network', 'type' => $tag->type == Tag::TYPE_SPACE ? 'space' : 'user', 'tag' => $tag->name]) ?>">
                                <span style="background-image: url('<?= $tag->getCover() ? $tag->getCover()->getUrl() : Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-tag-cover.png' ?>'); "></span>
                            </a>
                        </label>
                    <?php endforeach; ?>
                    <?php OwlCarouselWidget::end(); ?>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="row header">
                <span class="col-md-6 num-projects">
                    <?= $count . ' ' . ($type == NetworkController::TYPE_USER ? Yii::t('XcoinModule.network', 'User') : Yii::t('XcoinModule.network', 'Space')) . ($count != 1 ? 's' : '') ?>
                </span>
            </div>
            <div class="panels">
                <?php if ($type == NetworkController::TYPE_USER): ?>
                    <?php foreach ($results as $user): ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="user panel">
                                <div class="panel-heading">
                                    <?= UserImage::widget([
                                        'user' => $user,
                                        'width' => 75,
                                        'showTooltip' => false,
                                        'link' => true,
                                        'htmlOptions' => ['class' => 'user-img']
                                    ]); ?>
                                    <div class="user-info">
                                        <h4 class="user-fullname"><?= Html::encode($user->profile->firstname . " " . $user->profile->lastname) ?></h4>
                                        <h5 class="user-title"><?= Html::encode($user->profile->title) ?></h5>
                                        <h6 class="user-location">
                                            <?= Html::encode($user->profile->city) ?>
                                            <?= ($user->profile->city && $user->profile->country) ? ', ' : ' ' ?>
                                            <?= Iso3166Codes::country($user->profile->country) ?>
                                        </h6>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <p class="user-description">
                                        <?= Html::encode(StringUtils::shorten($user->profile->about, 100)) ?>
                                    </p>

                                    <ul class="user-tags">
                                        <?php foreach (explode(',', $user->tags) as $tag) : ?>
                                            <?php if (!empty($tag)) : ?>
                                                <li><?= Html::encode($tag) ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="panel-footer">
                                    <?= Html::a(
                                        '<i class="fa fa-search"></i>' . Yii::t('XcoinModule.network', 'See Profile'),
                                        $user->getUrl(),
                                        ['class' => 'btn btn-info']
                                    ) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach ($results as $space): ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="space panel">
                                <div class="panel-heading text-center">
                                    <?= SpaceImage::widget([
                                        'space' => $space,
                                        'width' => 75,
                                        'showTooltip' => false,
                                        'link' => true,
                                        'htmlOptions' => ['class' => 'space-img']
                                    ]); ?>
                                    <div class="space-info">
                                        <h5 class="space-title"><?= Html::encode($space->name) ?></h5>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <p class="space-description">
                                        <?= Html::encode(StringUtils::shorten($space->description, 100)) ?>
                                    </p>
                                    <ul class="space-stats">
                                        <li><span class="count"><?= $space->getFollowerCount() ?></span><?= Yii::t('XcoinModule.network', 'Follower') . ($space->getFollowerCount() != 1 ? 's' : '') ?></li>
                                        <li><span class="count"><?= Membership::getSpaceMembersQuery($space)->active()->visible()->count() ?></span><?= Yii::t('XcoinModule.network', 'User') . (Membership::getSpaceMembersQuery($space)->active()->visible()->count() != 1 ? 's' : '') ?></li>
                                    </ul>
                                </div>
                                <div class="panel-footer">
                                    <?= Html::a(
                                        Yii::t('XcoinModule.network', 'Visit Space'),
                                        $space->getUrl(),
                                        ['class' => 'btn btn-info']
                                    ) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
