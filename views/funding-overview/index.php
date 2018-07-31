<?php

use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Funding;
?>
<div class="container">
    <div class="row">
        <div class="col-md-12 fundingPanels">

            <?php if (count($spaces) === 0): ?>
                <div class="panel">
                    <div class="panel-heading">
                        <?= Yii::t('XcoinModule.base', 'Crowd Funding'); ?>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-warning">
                            <?= Yii::t('XcoinModule.base', 'Currently there are no running crowd fundings!'); ?>
                        </div>
                    </div>
                    <br />
                </div>
            <?php endif; ?>

            <div class="row">
                <?php foreach ($spaces as $space): ?>
                    <?php
                    $fundings = Funding::findAll(['space_id' => $space->id]);
                    $oneAvailable = false;
                    foreach ($fundings as $funding) {
                        if ($funding->getBaseMaximumAmount() > 0) {
                            $oneAvailable = true;
                        }
                    }
                    if (!$oneAvailable) {
                        continue;
                    }
                    ?>
                    <div class="col-md-4">
                        <div class="panel">
                            <div class="panel-heading">
                                <?= Html::a('Invest', $space->createUrl('/xcoin/funding'), ['class' => 'btn btn-default pull-right']); ?>
                                <?= SpaceImage::widget(['space' => $space, 'width' => 32, 'showTooltip' => true, 'link' => true]); ?>
                                <?= Html::encode($space->name); ?>
                            </div>
                            <div class="panel-body">
                                <div class="media">
                                    <div class="media-left media-middle">
                                    </div>
                                    <div class="media-body">
                                        <?php if (empty($space->description)): ?>
                                            <center><span style="color:grey"><?= Yii::t('XcoinModule.base', 'No description available'); ?></span></center>
                                        <?php else: ?>
                                            <h4 class="media-heading"><?= Html::encode($space->description); ?></h4>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <?= Yii::t('XcoinModule.base', 'Requested assets:'); ?>
                                <div class="pull-right">
                                    <?php foreach ($fundings as $funding): ?>
                                        <?php if ($funding->getBaseMaximumAmount() > 0): ?>
                                            <?= SpaceImage::widget(['space' => $funding->asset->space, 'width' => 24, 'showTooltip' => true, 'link' => true]); ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</div>

<style>
    .fundingPanels .panel-body {
        height:60px;
    }
    .fundingPanels .panel-footer {
        height:45px;
    }
</style>