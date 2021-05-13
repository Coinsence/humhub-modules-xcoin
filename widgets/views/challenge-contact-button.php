<?php
/** @var Funding $funding */

/** @var \humhub\modules\xcoin\widgets\ChallengeContactButton $contactButton */

use humhub\modules\xcoin\models\Funding;
use humhub\widgets\ActiveForm;
use yii\helpers\Html;
use humhub\modules\content\widgets\richtext\RichTextField;

?>
<div class="center">
    <h4><?php echo "for my project :" . " " . $funding->challenge->title ?></h4>

    <div class="col-md-12">

        <?= Html::beginForm('','post',['enctype' => 'multipart/form-data']) ?>
        <label>Message</label>
        <?= Html::input('richtext', 'message', null, ['class' => 'form-group form-control','required'=>true]) ?>
        <label>Upload File</label>
        <?= Html::fileInput('file') ?>
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary form-group']) ?>
        <?= Html::endForm() ?>

</div>

