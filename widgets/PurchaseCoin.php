<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Mortadha Ghanmi <mortadha.ghanmi56@gmail.com>
 */

namespace humhub\modules\xcoin\widgets;

use humhub\components\Widget;
use Yii;

/**
 * Returns Purchase coin button
 */
class PurchaseCoin extends Widget
{
    public $contentContainer;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (
            !array_key_exists('coinPurchase', Yii::$app->params) ||
            !array_key_exists('coin', Yii::$app->params['coinPurchase']) ||
            !array_key_exists('bridge', Yii::$app->params['coinPurchase'])
        )
            return;

        return $this->render('@xcoin/widgets/views/purchase-coin', [
            'contentContainer' => $this->contentContainer,
            'name' => Yii::$app->params['coinPurchase']['coin']
        ]);
    }
}
