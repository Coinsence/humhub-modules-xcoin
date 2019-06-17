<?php

namespace humhub\modules\xcoin\grids;

use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\widgets\GridView;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;

class ExchangeGridView extends GridView
{

    public $query;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->dataProvider->pagination->pageParam = 'ltg-page';
        $this->dataProvider->sort->sortParam = 'ltg-sort';


        $this->columns = [
#            [
#                'attribute' => 'id',
#                'options' => [
#                    'style' => 'width:50px',
#                ]
#            ],
            [
                'attribute' => 'asset_id',
                'label' => Yii::t('XcoinModule.base', 'Offer'),
                'format' => 'raw',
                'options' => ['style' => 'width:50px'],
                'value' => function ($model) {
                    return SpaceImage::widget(['space' => $model->asset->space, 'width' => 26, 'showTooltip' => true, 'link' => true]);
                }
            ],
            [
                'attribute' => 'wanted_asset_id',
                'label' => Yii::t('XcoinModule.base', 'Request'),
                'format' => 'raw',
                'options' => ['style' => 'width:50px'],
                'value' => function ($model) {
                    return SpaceImage::widget(['space' => $model->wantedAsset->space, 'width' => 26, 'showTooltip' => true, 'link' => true]);
                }
            ],
            [
                'label' => Yii::t('XcoinModule.base', 'Price per unit'),
                'options' => ['style' => 'width:120px'],
                'attribute' => 'exchange_rate',
                /*
                'value' => function($model) {
                    return '<strong>' . $model->exchange_rate . '</strong>:1';
                }
                */
            ],
            [
                'attribute' => 'available_amount',
                'label' => Yii::t('XcoinModule.base', 'Available'),
                'options' => ['style' => 'width:120px'],
                'value' => function ($model) {
                    return $model->getAvailableAmountValidated();
                }
            ],
            [
                'class' => AccountColumn::class,
                'accountAttribute' => 'account',
                'label' => Yii::t('XcoinModule.base', 'Offer Account')
            ],
            [
                'options' => ['style' => 'width:100px'],
                'format' => 'raw',
                'value' => function ($model) {

                    if (Yii::$app->user->isGuest) {
                        return Html::a('Buy', Yii::$app->user->loginUrl, ['class' => 'btn btn-success btn-sm', 'data-target' => '#globalModal']);
                    }

                    $o = '';
                    $o .= Html::a('Buy', ['/xcoin/exchange/buy', 'exchangeId' => $model->id], ['class' => 'btn btn-success btn-sm', 'data-target' => '#globalModal']);
                    if (Yii::$app->user->id == $model->created_by) {
                        $o .= "&nbsp;" . Html::a('Delete', ['/xcoin/exchange/delete', 'exchangeId' => $model->id], ['class' => 'btn btn-danger btn-sm']);
                    }
                    return $o;

                }
            ],
        ];


        parent::init();
    }

}
