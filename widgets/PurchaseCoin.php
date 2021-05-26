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
use humhub\modules\user\models\User;
use Yii;

/**
 * Returns Purchase coin button
 */
class PurchaseCoin extends Widget
{
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
        
        $identity = Yii::$app->user->identity;

        if ($identity === null)
            return;

        $user = User::findIdentity($identity->id);

        return $this->render('@xcoin/widgets/views/purchase-coin', [
            'contentContainer' => $user,
            'name' => Yii::$app->params['coinPurchase']['coin']
        ]);
    }
}
