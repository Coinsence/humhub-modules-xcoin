<?php

namespace humhub\modules\xcoin\helpers;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Asset;

/**
 * Description of AccountHelper
 *
 * @author Luke
 */
class AssetHelper
{

    public static function initContentContainer(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            if (Asset::find()->andWhere(['space_id' => $container->id])->count() == 0) {
                // Auto create asset
                $asset = new Asset();
                $asset->title = 'DEFAULT';
                $asset->space_id = $container->id;
                $asset->save();
            }
        }
    }

    public static function getSpaceAsset(Space $space) {
        return Asset::find()->andWhere(['space_id' => $space->id])->one();
    }


    public static function getAssetsDropDown(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            $assets = [];
            foreach (Asset::find()->andWhere(['space_id' => $container->id])->all() as $asset) {
                $assets[$asset->id] = $asset->title;
            }
            return $assets;
        }

        return [];
    }

    public static function createAccount(ContentContainerActiveRecord $container)
    {
        $account = new Account();

        if ($container instanceof Space) {
            $account->space_id = $container->id;
        }
        $account->user_id = Yii::$app->user->id;

        return $account;
    }

    public static function canManageAssets(ContentContainerActiveRecord $container, User $user = null)
    {
        if ($user === null) {
            if (Yii::$app->user->isGuest) {
                return false;
            }

            $user = Yii::$app->user->getIdentity();
        }

        if ($container instanceof Space) {
            if ($container->isSpaceOwner($user->id)) {
                return true;
            }
        }

        return false;
    }

}
