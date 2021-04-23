<?php
  humhub\assets\TabbedFormAsset::register($this);
?>

<div class="panel-heading">
    <?= Yii::t('XcoinModule.config', '<strong>Xcoin</strong> module configuration'); ?> <?php echo \humhub\widgets\DataSaved::widget(); ?>
</div>

<?= \humhub\modules\xcoin\widgets\ConfigurationMenu::widget(['space' => $space]) ?>

<div class="panel-body">
    <?php echo $content; ?>
</div>
