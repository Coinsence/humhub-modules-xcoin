<?php

namespace humhub\modules\xcoin\widgets;

use humhub\modules\xcoin\models\Account;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;

/**
 * UserTagsWidget lists all skills/tags of the user
 *
 * @package humhub.modules_core.user.widget
 * @since 0.5
 * @author andystrobel
 */
class UserCoin extends \yii\base\Widget
{
    /**
     * @var User user
     */
    public $user;

    /**
     * @var \humhub\modules\space\models\Space the Space which this header belongs to
     */
    public $space;
    /**
     * @var string css classes
     */
    public $cssClass;
 
    public function run()
    {
        $coins = [];
        foreach (AccountHelper::getAccounts($this->user) as $account) {
            if ($account->account_type !== Account::TYPE_DEFAULT) {
                continue;
            }
            foreach ($account->getAssets() as $asset) {
                $coins[] = '<div class="coin">' .
                    SpaceImage::widget(['space' => $asset->space, 'width' => 24, 'showTooltip' => true, 'link' => true]) .
                    '<span class="amountCoin">' . $account->getAssetBalance($asset) . '</span>' .
                '</div>';
            }
        }

        return $this->render('userCoin', [
            'user' => $this->user, 
            'coins' => implode('', $coins),
            'cssClass' => $this->cssClass
        ]);
    }
}

?>
