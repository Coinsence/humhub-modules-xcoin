<?php

namespace humhub\modules\xcoin\grids;

use Yii;
use yii\data\ArrayDataProvider;
use humhub\widgets\GridView;
use humhub\modules\xcoin\models\Account;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use yii\bootstrap\Html;

/**
 * Description of LatestTransactionsGridView
 *
 * @author Luke
 */
class ShareholderGridView extends GridView
{

    /**
     * @var Account
     */
    public $asset;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->dataProvider = new ArrayDataProvider([
            'allModels' => \humhub\modules\xcoin\widgets\AssetDistribution::getDistributionArray($this->asset),
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'attributes' => ['percent', 'balance'],
            ],
        ]);

        $this->columns = [
            [
                'format' => 'raw',
                'label' => '',
                'options' => ['style' => 'width:34px'],
                'value' => function($model) {
                    if ($model['record'] instanceof Space) {
                        return SpaceImage::widget(['space' => $model['record'], 'width' => 34, 'showTooltip' => false, 'link' => true]);
                    } else {
                        return UserImage::widget(['user' => $model['record'], 'width' => 34, 'showTooltip' => false, 'link' => true]);
                    }
                }
            ],
            [
                'format' => 'raw',
                'label' => '',
                'value' => function($model) {
                    if ($model['record'] instanceof Space) {
                        return Html::tag('strong', Html::encode($model['record']->name)) . '<br> ' . Yii::t('XcoinModule.base', 'Space');
                    } else {
                        return Html::tag('strong', Html::encode($model['record']->displayName)) . '<br> ' . Yii::t('XcoinModule.base', 'User');
                    }
                }
            ],
            [
                'format' => 'raw',
                'label' => 'Shares',
                'attribute' => 'balance',
                'options' => ['style' => 'width:160px'],
                'value' => function($model) {
                    return $model['balance'];
                }
            ],
            [
                'format' => 'raw',
                'label' => 'Percentage',
                'options' => ['style' => 'width:160px'],
                'attribute' => 'percent',
                'value' => function($model) {
                    return Html::tag('span', $model['percent']) . '%';
                }
            ],
        ];


        parent::init();
    }

}
