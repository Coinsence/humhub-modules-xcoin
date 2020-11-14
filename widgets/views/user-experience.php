<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

use humhub\libs\Iso3166Codes;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Experience;
use yii\helpers\Html;

/** @var $user User */
/** @var $experiences Experience[] */
/** @var $htmlOptions [] */
?>

<!-- // TODO move styling in less files -->

<?= Html::beginTag('div', $htmlOptions) ?>

<div class="row" style="display: flex;align-items: baseline;">
    <div class="col col-md-11">
        <h3><?= Yii::t('XcoinModule.experience', 'Experience') ?></h3>
    </div>
    <?php if (Yii::$app->user->id === $user->id) : ?>
        <div class="col col-md-1">
            <?= Html::a(
                '<i class="fa fa-plus"></i>',
                ['/xcoin/experience/edit', 'container' => $user],
                [
                    'data-target' => '#globalModal',
                    'class' => 'btn btn-primary-i-a tag',
                    'title' => Yii::t('XcoinModule.experience', 'Add Experience')
                ]
            ) ?>
        </div>
    <?php endif; ?>
</div>

<?php if (count($experiences) == 0) : ?>
    <p class="alert alert-warning col-md-12">
        <?= Yii::t('XcoinModule.experience', 'No experiences found.') ?>
    </p>
<?php endif; ?>
<?php foreach ($experiences as $experience) : ?>
    <div style="margin-bottom: 30px">
        <div class="row" style="display: flex;align-items: baseline;">
            <div class="col-md-10">
                <h4 style="color: black">
                    <span style="font-size: 13px;">&#x25cf;</span> <?= Html::encode($experience->position) ?>
                </h4>
                <h5 style="color: #3CBEEF"><?= Html::encode($experience->employer) ?></h5>
            </div>
            <?php if (Yii::$app->user->id === $user->id) : ?>
                <div class="col-md-2">
                    <?= Html::a(
                        '<i class="fa fa-pencil"></i>',
                        ['/xcoin/experience/edit', 'container' => $user, 'id' => $experience->id],
                        [
                            'data-target' => '#globalModal',
                            'class' => 'btn btn-primary-i-a tag',
                            'title' => Yii::t('XcoinModule.experience', 'Edit Experience')
                        ]
                    ) ?>
                    <?= Html::a(
                        '<i class="fa fa-times" style="color: red"></i>',
                        ['/xcoin/experience/delete', 'container' => $user, 'id' => $experience->id],
                        [
                            'data' => ['confirm' => Yii::t('XcoinModule.experience', 'Are you sure about deleting this experience')],
                            'class' => 'btn btn-primary-i-a tag',
                            'title' => Yii::t('XcoinModule.experience', 'Delete Experience'),
                        ]
                    ) ?>
                </div>
            <?php endif; ?>
        </div>
        <small>
            <?= date('F, Y', strtotime($experience->start_date)) ?>
            <?= ' - ' . ($experience->end_date ? date('F, Y', strtotime($experience->end_date)) : Yii::t('XcoinModule.experience', 'Today')) ?>
            &#x25cf;
            <?= Html::encode($experience->city) ?><?= ($experience->city && $experience->country) ? ', ' : ' ' ?><?= Iso3166Codes::country($experience->country) ?>
        </small>
        <p style="margin-top: 15px"><?= Html::encode($experience->description) ?></p>
    </div>
<?php endforeach; ?>

<?= Html::endTag('div') ?>
