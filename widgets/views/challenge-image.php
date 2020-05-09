<?php

use humhub\modules\xcoin\models\Challenge;
use yii\bootstrap\Html;

/** @var $challenge Challenge  */
/** @var $link bool  */
/** @var $title bool  */
/** @var $linkOptions []  */
/** @var $imageHtmlOptions []  */
?>
<?php if ($link) : echo Html::beginTag('a', $linkOptions); endif; ?>

<?php if ($challenge->getCover()) : ?>
    <?= Html::img($challenge->getCover()->getUrl(), $imageHtmlOptions) ?>
<?php else : ?>
    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-challenge-cover.png', $imageHtmlOptions) ?>
<?php endif ?>

<?php if ($title): ?>
    <small><?= Html::encode($challenge->title) ?></small>
<?php endif; ?>

<?php if ($link) :  echo Html::endTag('a'); endif; ?>