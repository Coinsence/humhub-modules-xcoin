<?php

use humhub\modules\xcoin\models\Marketplace;
use yii\bootstrap\Html;

/** @var $marketplace Marketplace  */
/** @var $link bool  */
/** @var $title bool  */
/** @var $linkOptions []  */
/** @var $imageHtmlOptions []  */
?>
<?php if ($link) : echo Html::beginTag('a', $linkOptions); endif; ?>

<?php if ($marketplace->getCover()) : ?>
    <?= Html::img($marketplace->getCover()->getUrl(), $imageHtmlOptions) ?>
<?php else : ?>
    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-marketplace-cover.png', $imageHtmlOptions) ?>
<?php endif ?>

<?php if ($link) :  echo Html::endTag('a'); endif; ?>

<?php if ($title): ?>
    <?= Html::encode($marketplace->title) ?>
<?php endif; ?>
