<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\xcoin\widgets;

use Yii;
use yii\base\Widget;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\space\widgets\Image as SpaceImage;

/**
 * Description of ExchangeFilter
 *
 * @author Luke
 */
class ExchangeFilter extends \humhub\components\Widget
{

    public function run()
    {
        $assetList = [];
        foreach (Asset::find()->all() as $asset) {
            $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $asset->space->name;
        }

        return $this->render('exchange-filter', ['assetList' => $assetList, 'filters' => self::getFilters()]);
    }

    public static function applyFilters(\yii\db\ActiveQuery $query)
    {
        $filters = self::getFilters();

        if (!empty($filters['from'])) {
            $query->andWhere(['asset_id' => $filters['from']]);
        }

        if (!empty($filters['to'])) {
            $query->andWhere(['wanted_asset_id' => $filters['to']]);
        }

        if (!empty($filters['mine'])) {
            $query->andWhere(['created_by' => Yii::$app->user->id]);
        }


        return $query;
    }

    public static function getFilters()
    {
        $filters = [
            'mine' => Yii::$app->request->get('filter-mine', false),
            'from' => Yii::$app->request->get('filter-from', ''),
            'to' => Yii::$app->request->get('filter-to', '')
        ];

        return $filters;
    }

}
