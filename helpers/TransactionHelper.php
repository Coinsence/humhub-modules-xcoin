<?php

namespace humhub\modules\xcoin\helpers;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Transaction;

/**
 * Description of AccountHelper
 *
 * @author Luke
 */
class TransactionHelper
{

    public static function getLatest(ContentContainerActiveRecord $container)
    {
        $query = Transaction::find();
        $query->joinWith(['fromAccount fromAccount', 'toAccount toAccount']);

        $accountQuery = AccountHelper::getAccountsQuery($container)->select('id');
        $query->andWhere(['in', 'from_account_id', $accountQuery]);
        $query->orWhere(['in', 'to_account_id', $accountQuery]);

        $query->addOrderBy(['xcoin_transaction.id' => SORT_DESC]);

        return $query;
    }

    public static function getAssetLatest(Asset $asset)
    {
        $query = Transaction::find();

        /*
          $query->joinWith(['fromAccount fromAccount', 'toAccount toAccount']);
          $accountQuery = AccountHelper::getAccountsQuery($container)->select('id');
          $query->andWhere(['in', 'asset_', $accountQuery]);
          $query->orWhere(['in', 'to_account_id', $accountQuery]);
         */

        $query->andWhere(['asset_id' => $asset->id]);

        $query->addOrderBy(['xcoin_transaction.id' => SORT_DESC]);

        return $query;
    }

    public static function getTypeTitle($type)
    {
        if ($type == Transaction::TRANSACTION_TYPE_ISSUE) {
            return 'Issue';
        } elseif ($type == Transaction::TRANSACTION_TYPE_TRANSFER) {
            return 'Transfer';
        }

        return 'Unknown';
    }

}
