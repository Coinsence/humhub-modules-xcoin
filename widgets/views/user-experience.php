<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

use humhub\libs\Iso3166Codes;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Experience;
use yii\helpers\Html;

/** @var $user User */
/** @var $experiences Experience[] */
/** @var $htmlOptions [] */
?>
 
<!-- // TODO move styling in less files -->

<?= Html::beginTag('div') ?>

<div class="row experience" style="display: flex;align-items: baseline;position: relative;">
   
        <h2><?= Yii::t('XcoinModule.experience', 'Experience') ?></h2>
    
    <?php if (Yii::$app->user->id === $user->id) : ?>
       
            <?= Html::a(
                '<i class="fa fa-plus"></i>',
                ['/xcoin/experience/edit', 'container' => $user],
                [  
                    'data-target' => '#globalModal',
                    'class' => 'btn btn-primary-i-a tag addExperience',
                    'title' => Yii::t('XcoinModule.experience', 'Add Experience')
                ]
            ) ?>
       
    <?php endif; ?>
    <?php if (count($experiences) == 0) : ?>
      <p class="alert alert-warning col-md-12">
          <?= Yii::t('XcoinModule.experience', 'No experiences found.') ?>
      </p>
    <?php endif; ?>
    <?php foreach ($experiences as $experience) : ?>
    <ul>
        <li>
          <h3 class="position"> <?= Html::encode($experience->position) ?></h3>
          <h3 class="companyName"><?= Html::encode($experience->employer) ?></h3>
           
            <?php if (Yii::$app->user->id === $user->id) : ?>
                <div class="editExperience">
                    <?= Html::a(
                        '<i class="fas fa-pencil-alt"></i>',
                        ['/xcoin/experience/edit', 'container' => $user, 'id' => $experience->id],
                        [
                            'data-target' => '#globalModal',
                            'class' => 'btn btn-primary-i-a tag pencilEdit',
                            'title' => Yii::t('XcoinModule.experience', 'Edit Experience')
                        ]
                    ) ?>
                    <?= Html::a(
                        '<i class="fa fa-times" style="color: red"></i>',
                        ['/xcoin/experience/delete', 'container' => $user, 'id' => $experience->id],
                        [
                            'data' => ['confirm' => Yii::t('XcoinModule.experience', 'Are you sure about deleting this experience')],
                            'class' => 'btn btn-primary-i-a tag deleteExperience',
                            'title' => Yii::t('XcoinModule.experience', 'Delete Experience'),
                        ]
                    ) ?>
               </div>
            <?php endif; ?>
        <h3 class="dateAndPlace">
          <span class="date"> <?= date('F, Y', strtotime($experience->start_date)) ?>
                    <?= ' - ' . ($experience->end_date ? date('F, Y', strtotime($experience->end_date)) : Yii::t('XcoinModule.experience', 'Today')) ?>
                  <b>.</b> 
          </span>
          <span class="place"><?= Html::encode($experience->city) ?><?= ($experience->city && $experience->country) ? ', ' : ' ' ?><?= Iso3166Codes::country($experience->country) ?>
          </span>
        </h3>
        
        <p class="moreDetails"><?= Html::encode($experience->description) ?></p>
      </li>
    </ul>

<?php endforeach; ?>
</div>
<?= Html::endTag('div') ?>
