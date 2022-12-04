<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2022 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author : Ala Daly <rafin_ala03@hotmail.fr>
 * @contributer Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\widgets;

use humhub\modules\algorand\calls\Coin;
use humhub\modules\algorand\utils\Helpers;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

class UserCoin extends \yii\base\Widget
{
    /**
     * @var User user
     */
    public $user;

    /**
     * @var Space the Space which this header belongs to
     */
    public $space;
    /**
     * @var string css classes
     */
    public $cssClass;

    public function run()
    {
        $coins = [];

        $account = AccountHelper::getDefaultAccount($this->user);

        foreach ($account->getAssets() as $asset) {

            $coinBalance = Coin::balance($account, $asset);

            if (null === $coinBalance) {
                continue;
            }

            $coins[] = '<div class="coin">' .
                SpaceImage::widget(['space' => $asset->space, 'width' => 24, 'showTooltip' => true, 'link' => true]) .
                '<span class="amountCoin">' . Helpers::formatCoinAmount($coinBalance->amount, true) . '</span>' .
                '</div>';
        }

        return $this->render('userCoin', [
            'user' => $this->user,
            'coins' => implode('', $coins),
            'cssClass' => $this->cssClass
        ]);
    }
}

