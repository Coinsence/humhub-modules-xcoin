<?php

namespace humhub\modules\xcoin\helpers;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\permissions\ReviewSubmittedProjects;
use humhub\modules\xcoin\permissions\SellSpaceProducts;
use humhub\modules\xcoin\permissions\SubmitSpaceProjects;
use Yii;

/**
 * SpaceHelper
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class SpaceHelper
{
    public static function generateRandomSpaceName($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    public static function allowDirectCoinTransfer(Account $account)
    {
        if (!$account->space_id)
            return null;

        $space = Space::findOne(['id' => $account->space_id]);
        $module = Yii::$app->getModule('xcoin');

        return $module->settings->contentContainer($space)->get('allowDirectCoinTransfer');
    }

    public static function canSubmitProject(ContentContainerActiveRecord $container)
    {
        return $container->permissionManager->can(new SubmitSpaceProjects());
    }

    public static function canSellProduct(ContentContainerActiveRecord $container)
    {
        return $container->permissionManager->can(new SellSpaceProducts());
    }

    public static function canReviewProject(ContentContainerActiveRecord $container)
    {
        return $container->permissionManager->can(new ReviewSubmittedProjects());
    }

    public static function getSubmitterSpaces(User $user)
    {
        $spaces = [];

        foreach (Membership::find()->where(['user_id' => $user->id])->all() as $membership) {
            if (self::canSubmitProject($membership->space)) {
                $spaces[] = $membership->space;
            }
        }

        return $spaces;
    }

    public static function getSellerSpaces(User $user)
    {
        $spaces = [];

        foreach (Membership::find()->where(['user_id' => $user->id])->all() as $membership) {
            if (self::canSellProduct($membership->space)) {
                $spaces[] = $membership->space;
            }
        }

        return $spaces;
    }

    public static function canAddSpaceToListForProject(Challenge $challenge, Space $space)
    {
        return $challenge->acceptAnyRewardingAsset() && AssetHelper::getSpaceAsset($space) && AssetHelper::getSpaceAsset($space)->id != $challenge->asset_id;
    }
}
