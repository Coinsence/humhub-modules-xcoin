<?php

namespace humhub\modules\xcoin\helpers;

use humhub\components\Event;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\permissions\CreateAccount;
use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\xcoin\models\Account;
use yii\base\Exception;
use yii\web\HttpException;
use humhub\modules\space\widgets\Image as SpaceImage;

/**
 * AccountHelper
 *
 * @author Luke
 * @contributer Daly Ghaith <daly.ghaith@gmail.com>
 */
class AccountHelper
{

    public static function initContentContainer(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            if (Account::find()->andWhere(['space_id' => $container->id])->count() == 0) {
                $account = new Account();
                $account->title = 'Default';
                $account->space_id = $container->id;
                $account->account_type = Account::TYPE_DEFAULT;
                $account->save();

                Event::trigger(Account::class, Account::EVENT_DEFAULT_SPACE_ACCOUNT_CREATED, new Event(['sender' => $container]));
            }
        } else {
            if (Account::find()->andWhere(['user_id' => $container->id])->count() == 0) {
                $account = new Account();
                $account->title = 'Default';
                $account->user_id = $container->id;
                $account->account_type = Account::TYPE_DEFAULT;
                $account->save();
            }
        }
    }

    /**
     * @param ContentContainerActiveRecord $container
     * @param Asset|null $asset
     * @return \yii\db\ActiveQuery
     */
    public static function getAccountsQuery(ContentContainerActiveRecord $container, Asset $asset = null)
    {
        if ($container instanceof Space) {
            $query = Account::find()
                ->andWhere(['space_id' => $container->id])
                ->andWhere(['!=', 'account_type', Account::TYPE_ISSUE])
                ->andWhere(['archived' => 0]);

            if ($asset) {
                $query
                    ->leftJoin('xcoin_transaction',
                        'xcoin_transaction.to_account_id = xcoin_account.id or ' .
                        'xcoin_transaction.from_account_id = xcoin_account.id'
                    )
                    ->andWhere("xcoin_transaction.asset_id = {$asset->id}");
            }

            return $query;
        } elseif ($container instanceof User) {
            $query = Account::find()
                ->andWhere(['user_id' => $container->id])
                ->andWhere(['not in', 'account_type', [Account::TYPE_ISSUE, Account::TYPE_TASK]])
                ->andWhere(['archived' => 0]);

            if ($asset) {
                $query
                    ->leftJoin('xcoin_transaction',
                        'xcoin_transaction.to_account_id = xcoin_account.id or ' .
                        'xcoin_transaction.from_account_id = xcoin_account.id'
                    )
                    ->andWhere("xcoin_transaction.asset_id = {$asset->id}");
            }

            return $query;
        }
    }


    /**
     * @param ContentContainerActiveRecord $container
     * @return Account[] a list of accounts
     */
    public static function getAccounts(ContentContainerActiveRecord $container)
    {
        return self::getAccountsQuery($container)->all();
    }

    public static function getAccountsDropDown(ContentContainerActiveRecord $container)
    {
        $accounts = [];
        foreach (self::getAccounts($container) as $account) {
            $accounts[$account->id] = $account->title;
        }

        return $accounts;
    }

    public static function getFundingAccount(Funding $funding)
    {
        $account = Account::findOne(['funding_id' => $funding->id]);

        if ($account === null) {
            throw new HttpException(404);
        }

        return $account;
    }

    public static function getIssueAccount(Space $space)
    {
        $issueAccount = Account::findOne(['space_id' => $space->id, 'account_type' => Account::TYPE_ISSUE]);
        if ($issueAccount === null) {
            $issueAccount = new Account();
            $issueAccount->space_id = $space->id;
            $issueAccount->title = 'Asset Issues';
            $issueAccount->account_type = Account::TYPE_ISSUE;
            if (!$issueAccount->save()) {
                throw new Exception('Could not create issue account!');
            }
        }

        return $issueAccount;
    }

    public static function createAccount(ContentContainerActiveRecord $container)
    {
        $account = new Account();

        if ($container instanceof Space) {
            $account->space_id = $container->id;
        }

#        $account->user_id = Yii::$app->user->id;

        return $account;
    }

    public static function canManageAccount(Account $account)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $user = Yii::$app->user->getIdentity();

        if ($account->account_type == Account::TYPE_ISSUE) {
            return false;
        }

        if ($account->user_id == $user->id) {
            return true;
        }

        if ($account->space !== null && $account->space->isAdmin($user->id)) {
            return true;
        }


        return false;
    }

    public static function canCreateAccount(ContentContainerActiveRecord $container)
    {
        return $container->permissionManager->can(new CreateAccount());
    }

    public static function getFundingAccountBalance(Funding $funding, $requested = true)
    {
        if ($requested) {
            $asset = Asset::findOne(['id' => $funding->getChallenge()->one()->asset_id]);
        } else {
            $asset = AssetHelper::getSpaceAsset($funding->space);
        }

        return AccountHelper::getFundingAccount($funding)->getAssetBalance($asset);
    }

    public static function getAssetsList(Account $account)
    {
        $accountAssetList = [];
        foreach ($account->getAssets() as $asset) {
            $max = $account->getAssetBalance($asset);
            if (!empty($max)) {
                $accountAssetList[$asset->id] = SpaceImage::widget([
                        'space' => $asset->space,
                        'width' => 16,
                        'showTooltip' => true,
                        'link' => true])
                    . ' ' . $asset->space->name . '<small class="pull-rightx"> - max. ' . $max . '</small>';
            }
        }

        return $accountAssetList;
    }

}
