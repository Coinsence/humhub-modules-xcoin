<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Projectplace;
use yii\helpers\Url;
use yii\helpers\Html;

Assets::register($this);

/** @var $projectPlaces Projectplace[] */

?>

<div class="space-challenges">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><?= Yii::t('XcoinModule.Projectplace', 'Space Projectplaces') ?></strong>
        </div>
        <div class="panel-body">
            <div class="panels">
                <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">

                        <a class="add-challenge"
                           href="<?= Url::to(['/xcoin/projectplace/create', 'container' => $this->context->contentContainer]) ?>"
                           data-target="#globalModal">
                            <span class="icon">
                                <i class="cross"></i>
                            </span>
                            <span class="text"><?= Yii::t('XcoinModule.Projectplace', 'Add a Projectplace!') ?></span>
                        </a>
                    </div>
                <?php else: ?>
                    <?php if (empty($projectPlaces)): ?>
                        <p class="alert alert-warning col-md-12">
                            <?= Yii::t('XcoinModule.Projectplace', 'Currently there are no projectplaces for this space.') ?>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>

                <?php foreach ($projectPlaces as $projectPlace): ?>
                    <a href="<?= $projectPlace->space->createUrl('/xcoin/projectplace/overview', [
                        'projectPlaceId' => $projectPlace->id
                    ]); ?>">
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="panel">
                                <div class="panel-heading">
                                    <!-- projectplace image start -->
                                        <div class="bg" style="background-image: url('<?= $projectPlace->getCover()->getUrl() ?>')"></div>
                                        <?= Html::img($projectPlace->getCover()->getUrl(), ['height' => '240']) ?>
                                    <!-- projectplace image end -->
                                </div>
                                <div class="panel-body">
                                    <!-- projectplace title start -->
                                    <h5 class="challenge-title">
                                        <?= Html::encode($projectPlace->title); ?>
                                    </h5>
                                    <!-- projectplace title end -->
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
