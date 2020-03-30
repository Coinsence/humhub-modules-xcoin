<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Challenge;


Assets::register($this);

/** @var $challenge Challenge */

?>

<div class="space-challenge">
    <div class="panel panel-default">
        <?php
        $space = $challenge->getSpace()->one();
        $cover = $challenge->getCover();
        ?>
        <div class="panel-heading">
            <h1><?= $challenge->title ?></h1>
        </div>
        <div class="panel-body">

        </div>
    </div>
</div>

