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

/** @var $tags Tag[] */
/** @var $results Space[]|User[] */
/** @var $type string */

$count = count($results);
?>
<style>
    .crowd-funding .content .panel .panel-body {
        margin-top: 0;
    }
</style>
<div class="crowd-funding">
    <div class="filters">
        <div class="container">
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
                    <label class="category all">
                        <input type="radio" name="categroy" checked>
                        <a href="<?= Url::to(['/xcoin/network', 'type' => 'user']) ?>">
                            <span>
                                <label><?= Yii::t('XcoinModule.network', 'All Users') ?></label>
                            </span>
                        </a>
                    </label>
                    <label class="category all">
                        <input type="radio" name="categroy" checked>
                        <a href="<?= Url::to(['/xcoin/network', 'type' => 'space']) ?>">
                            <span>
                                <label><?= Yii::t('XcoinModule.network', 'All Spaces') ?></label>
                            </span>
                        </a>
                    </label>
                    <?php foreach ($tags as $tag): ?>
                        <label class="category">
                            <a href="<?= Url::to(['/xcoin/network', 'type' => $tag->type == Tag::TYPE_SPACE ? 'space' : 'user', 'tag' => $tag->name]) ?>">
                                <span style="background-image: url('<?= $tag->getCover() ? $tag->getCover()->getUrl() : Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-tag-cover.png' ?>'); ">
                                    <label><?= $tag->name ?></label>
                                </span>
                            </a>
                        </label>
                    <?php endforeach; ?>
                    <?php OwlCarouselWidget::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container">
            <div class="row header">
                <div class="col-md-6">
                    <span class="num-projects">
                        <?= $count . ' ' . ($type == NetworkController::TYPE_USER ? Yii::t('XcoinModule.network', 'User') : Yii::t('XcoinModule.network', 'Space')) . ($count != 1 ? 's' : '') ?>
                    </span>
                </div>
            </div>
            <div class="panels">
                <?php if ($type == NetworkController::TYPE_USER): ?>
                    <?php foreach ($results as $user): ?>
                        <div class="col-sm-6 col-md-4 col-lg-3" style="height: 350px">
                            <div class="panel">
                                <div class="panel-heading">
                                    <?= UserImage::widget([
                                        'user' => $user,
                                        'width' => 60,
                                        'showTooltip' => false,
                                        'link' => true
                                    ]); ?>
                                    <strong><?= Html::encode($user->profile->firstname . " " . $user->profile->lastname) ?></strong><br>
                                    <small><?= Html::encode($user->profile->title) ?></small><br>
                                    <small>
                                        <?= Html::encode($user->profile->city) ?>
                                        <?= ($user->profile->city && $user->profile->country) ? ', ' : ' ' ?>
                                        <?= Iso3166Codes::country($user->profile->country) ?>
                                    </small>
                                </div>
                                <div class="panel-body">
                                    <?= Html::encode(StringUtils::shorten($user->profile->about, 100)) ?><br><br>

                                    <?php $userTags = explode(',', $user->tags) ?>
                                    <?php foreach ($userTags as $tag) : ?>
                                        <?php if (!empty($tag)) : ?>
                                            <b class="btn btn-default"><?= Html::encode($tag) ?></b>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <div class="panel-footer text-center" style="margin-top: 20px">
                                    <?= Html::a(
                                        '<i class="fa fa-user-plus"></i>' . Yii::t('XcoinModule.network', 'Connect'),
                                        $user->getUrl(),
                                        ['class' => 'btn btn-info btn-lg']
                                    ) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach ($results as $space): ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="panel">
                                <div class="panel-heading text-center">
                                    <?= SpaceImage::widget([
                                        'space' => $space,
                                        'width' => 60,
                                        'showTooltip' => false,
                                        'link' => true
                                    ]); ?>
                                    <h5><b><?= Html::encode($space->name) ?></b></h5>
                                </div>
                                <div class="panel-body text-center">
                                    <?= Html::encode(StringUtils::shorten($space->description, 100)) ?><br><br>

                                    <b class="btn btn-default"><?= $space->getFollowerCount() ?> <?= Yii::t('XcoinModule.network', 'Follower') . ($space->getFollowerCount() != 1 ? 's' : '') ?></b>
                                    <b class="btn btn-default"><?= Membership::getSpaceMembersQuery($space)->active()->visible()->count() ?> <?= Yii::t('XcoinModule.network', 'User') . (Membership::getSpaceMembersQuery($space)->active()->visible()->count() != 1 ? 's' : '') ?></b>
                                </div>
                                <div class="panel-footer text-center" style="margin-top: 20px">
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
