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
use humhub\modules\xcoin\helpers\AccountHelper;
use Yii;

/**
 * Returns Purchase coin button
 */
class PurchaseCoin extends Widget
{
    public $contentContainer;
    public $style;
    public $requireAsset;
    public $noCoinsWarning;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!self::isEnabled())
            return;
        
        $assetName = isset($this->requireAsset) ? $this->requireAsset->getSpace()->one()->name : '';

        if ($assetName !== '' && $assetName !== Yii::$app->params['coinPurchase']['space'])
            return;
        
        $identity = Yii::$app->user->identity;

        if ($identity === null)
            return;

        $user = User::findIdentity($identity->id);

        $noCoinsWarning = false;
        $currentCoinsBalance = 0;
        if ($this->noCoinsWarning) {
            $accounts = AccountHelper::getAccountsQuery($this->contentContainer, $this->requireAsset)->all();
            foreach ($accounts as $account) {
                foreach ($account->getAssets() as $asset) {
                    if ($assetName === $asset->space->name) {
                        $currentCoinsBalance += $account->getAssetBalance($asset);
                    }
                }
            }
            $noCoinsWarning = true;
        }

        return $this->render('@xcoin/widgets/views/purchase-coin', [
            'style' => $this->style,
            'contentContainer' => $user,
            'name' => Yii::$app->params['coinPurchase']['coin'],
            'noCoinsWarning' => $noCoinsWarning,
            'coinsBlanace' => $currentCoinsBalance,
            'asset' => $this->requireAsset
        ]);
    }

    static function isEnabled()
    {
        return
            array_key_exists('coinPurchase', Yii::$app->params) &&
            array_key_exists('coin', Yii::$app->params['coinPurchase']) &&
            array_key_exists('space', Yii::$app->params['coinPurchase']) &&
            array_key_exists('bridge', Yii::$app->params['coinPurchase']);
    }
}
