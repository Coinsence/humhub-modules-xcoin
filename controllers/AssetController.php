<?php

namespace humhub\modules\xcoin\controllers;

use humhub\components\Event;
use humhub\modules\xcoin\helpers\SpaceHelper;
use Yii;
use yii\web\HttpException;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Transaction;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\AccountHelper;

/**
 * Description of AccountController
 *
 * @author Luke
 */
class AssetController extends ContentContainerController
{

    public function beforeAction($action)
    {
        if (!AssetHelper::canManageAssets($this->contentContainer) && Yii::$app->user->isAdmin()) {
            throw new \yii\web\HttpException(401, 'Permission denied!');
        }

        return parent::beforeAction($action);
    }

    public function actionIssue($id)
    {
        $asset = Asset::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        
        if ($asset === null) {
            throw new HttpException(404);
        }
        if(!SpaceHelper::canIssueCoins($this->contentContainer)){
            throw new HttpException(403);
        }
        $issueAccount = AccountHelper::getIssueAccount($asset->space);

        $transaction = new Transaction();
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_ISSUE;
        $transaction->asset_id = $asset->id;
        $transaction->from_account_id = $issueAccount->id;


        $accounts = AccountHelper::getAccountsDropDown($this->contentContainer);
        unset($accounts[$issueAccount->id]);

        if ($transaction->load(Yii::$app->request->post())) {

            Event::trigger(Transaction::class, Transaction::EVENT_TRANSACTION_TYPE_ISSUE, new Event(['sender' => $transaction]));

            $transaction->save();

            $this->view->saved();

            return $this->htmlRedirect(['/xcoin/overview', 'container' => $this->contentContainer]);
        }

        return $this->renderAjax('issue', ['transaction' => $transaction, 'accounts' => $accounts]);
    }

}
