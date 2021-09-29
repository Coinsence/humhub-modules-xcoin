<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\widgets;

use humhub\components\Widget;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Asset;
use Yii;

/**
 * Returns Cashout coin button
 */
class CashoutCoinButton extends Widget
{
    /** @var ContentContainer */
    public $contentContainer;

    /** @var Asset */
    public $requireAsset;

    /** @var array */
    public $style;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!self::isEnabled() || null === $cashOutAsset = $this->getCashoutAsset()) {
            return;
        }

        $user = Yii::$app->user->identity;

        if ($user === null)
            return;

        return $this->render('@xcoin/widgets/views/cashout-coin-button', [
            'cashOutAsset' => $cashOutAsset,
            'cashOutAssetName' =>  Yii::$app->params['coinCashOut']['space'],
            'contentContainer' => $user,
            'style' => $this->style,
        ]);
    }

    static function isEnabled()
    {
        return
            array_key_exists('coinCashOut', Yii::$app->params) &&
            array_key_exists('space', Yii::$app->params['coinCashOut']) &&
            array_key_exists('bridge', Yii::$app->params['coinCashOut']) &&
            array_key_exists('redeem-account-eth-address', Yii::$app->params['coinCashOut']);

    }

    private function getCashoutAsset()
    {
        /** @var Space|null $cashOutSpace */
        $cashOutSpace = Space::findOne(['name' => Yii::$app->params['coinCashOut']['space']]);

        /** @var Account $cashOutAccount */
        $cashOutAccount = Account::findOne(['ethereum_address' => Yii::$app->params['coinCashOut']['redeem-account-eth-address']]);

        if (null === $cashOutSpace || null === $cashOutAccount) {
            return null;
        }

        if (null == $cashOutAsset = Asset::findOne(['space_id' => $cashOutSpace->id])) {
            return null;
        }

        return $cashOutAsset;
    }
}
