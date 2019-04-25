<?php

namespace humhub\modules\xcoin\helpers;

use humhub\components\Event;
use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\xcoin\models\Account;
use yii\redis\ActiveQuery;

/**
 * Description of AccountHelper
 *
 * @author Luke
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
     * @return ActiveQuery
     */
    public static function getAccountsQuery(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            return Account::find()->andWhere(['space_id' => $container->id]);
        } elseif ($container instanceof User) {
            return Account::find()->andWhere(['user_id' => $container->id]);
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

    public static function getFundingAccount(Space $space)
    {
        $account = Account::findOne(['space_id' => $space->id, 'account_type' => Account::TYPE_FUNDING]);
        if ($account === null) {
            $account = new Account();
            $account->space_id = $space->id;
            $account->title = 'Funding';
            $account->account_type = Account::TYPE_FUNDING;
            if (!$account->save()) {
                throw new Exception('Could not create funding account!');
            }
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

        if ($account->user_id === null && $account->space !== null && $account->space->isAdmin($user->id)) {
            return true;
        }


        return false;
    }

    public static function canCreateAccount(ContentContainerActiveRecord $container)
    {
        return $container->permissionManager->can(new \humhub\modules\xcoin\permissions\CreateAccount());

        /*
          if ($container instanceof User && $container->id != Yii::$app->user->id) {
          return false;
          }

          return true;
         *
         */
    }

    public static function getFundingAccountBalance($space)
    {
        $asset = AssetHelper::getSpaceAsset($space);

        return AccountHelper::getFundingAccount($space)->getAssetBalance($asset);
    }

}
