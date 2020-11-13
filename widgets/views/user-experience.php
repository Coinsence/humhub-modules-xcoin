<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

use humhub\libs\Iso3166Codes;
use humhub\modules\xcoin\models\Experience;
use yii\helpers\Html;

/** @var $experiences Experience[] */
/** @var $htmlOptions [] */
?>

<!-- // TODO move styling in less files -->

<?= Html::beginTag('div', $htmlOptions) ?>

<h3><?= Yii::t('XcoinModule.experience', 'Experience') ?></h3>
<?php if(count($experiences) == 0) :?>
    <p class="alert alert-warning col-md-12">
        <?= Yii::t('XcoinModule.experience', 'No experiences found.') ?>
    </p>
<?php endif; ?>
<?php foreach ($experiences as $experience) : ?>
    <div style="margin-bottom: 30px">
        <h4 style="color: black"><span style="font-size: 13px;">&#x25cf;</span> <?= Html::encode($experience->position) ?></h4>
        <h5 style="color: #3CBEEF"><?= Html::encode($experience->employer) ?></h5>
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
