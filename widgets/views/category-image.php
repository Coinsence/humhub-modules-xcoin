<?php

use humhub\modules\xcoin\models\Category;
use yii\bootstrap\Html;

/** @var $category Category  */
/** @var $imageHtmlOptions []  */
?>

<?php if ($category->getCover()) : ?>
    <?= Html::img($category->getCover()->getUrl(), $imageHtmlOptions) ?>
<?php else : ?>
    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-category-cover.png', $imageHtmlOptions) ?>
<?php endif ?>