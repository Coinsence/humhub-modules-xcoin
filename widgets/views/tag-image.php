<?php

use humhub\modules\xcoin\models\Tag;
use yii\bootstrap\Html;

/** @var $tag Tag  */
/** @var $imageHtmlOptions []  */
?>

<?php if ($tag->getCover()) : ?>
    <?= Html::img($tag->getCover()->getUrl(), $imageHtmlOptions) ?>
<?php else : ?>
    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-tag-cover.png', $imageHtmlOptions) ?>
<?php endif ?>