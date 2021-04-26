<?php

use humhub\libs\Html;

/** @var $contentContainer humhub\modules\content\components\ContentContainerActiveRecord */
/** @var $name string */

?>

<div>
    <?= Html::a('<i class="fa fa-money" aria-hidden="true"></i> Buy ' . $name, [
        '/xcoin/overview/purchase-coin',
        'container' => $contentContainer
    ], ['class' => 'btn btn-default', 'data-target' => '#globalModal']) ?>
</div>
