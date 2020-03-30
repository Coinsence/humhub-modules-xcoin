<?php

use humhub\modules\xcoin\models\Challenge;
use yii\bootstrap\Html;

/** @var $challenge Challenge  */
/** @var $link bool  */
/** @var $linkOptions []  */
/** @var $imageHtmlOptions []  */
?>
<?php if ($link) : echo Html::beginTag('a', $linkOptions); endif; ?>

<?php if ($challenge->getCover()) : ?>
    <?= Html::img($challenge->getCover()->getUrl(), $imageHtmlOptions) ?>
<?php else : ?>
    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-challenge-cover.png', $imageHtmlOptions) ?>
<?php endif ?>

<small><?= Html::encode($challenge->title) ?></small>

<?php if ($link) :  echo Html::endTag('a'); endif; ?>