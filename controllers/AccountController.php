<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\user\models\User;
use humhub\modules\xcoin\helpers\SpaceHelper;
use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\space\models\Space;
use yii\web\HttpException;

/**
 * Description of AccountController
 *
 * @author Luke
 */
class AccountController extends ContentContainerController
{

    /**
     * @param $id
     * @return string
     * @throws HttpException
     */
    public function actionIndex($id)
    {
        $account = Account::findOne(['id' => $id]);


        if ($account === null) {
            throw new HttpException(404);
        }

        // Module settings allowDirectCoinTransfer parameter value
        $allowDirectCoinTransfer = SpaceHelper::allowDirectCoinTransfer($account);

        return $this->render('index', ['account' => $account, 'allowDirectCoinTransfer' => $allowDirectCoinTransfer]);
    }

    public function actionEdit()
    {
        if($this->contentContainer instanceof User){
            $this->redirect($this->contentContainer->createUrl('/xcoin/overview'),302);
        }

        $account = Account::findOne(['id' => Yii::$app->request->get('id')]);
        if ($account === null) {
            if (!AccountHelper::canCreateAccount($this->contentContainer)) {
                throw new HttpException(401);
            }
            $account = AccountHelper::createAccount($this->contentContainer);

            if ($this->contentContainer instanceof Space) {
                if (!$this->contentContainer->isSpaceOwner()) {
                    $account->user_id = Yii::$app->user->id;
                }
            } else {
                $account->user_id = $this->contentContainer->id;
            }
        } elseif (!AccountHelper::canManageAccount($account)) {
            throw new HttpException(401);
        } elseif ($account->account_type != Account::TYPE_STANDARD) {
            throw new HttpException(401, 'You cannot edit this account type!');
        }

        if ($account->load(Yii::$app->request->post()) && $account->save()) {

            $this->view->saved();

            return $this->htmlRedirect(['/xcoin/account', 'id' => $account->id, 'container' => $this->contentContainer]);
        }

        return $this->renderAjax('edit', ['account' => $account]);
    }

    public function actionDisable($id)
    {
        if($this->contentContainer instanceof User){
            $this->redirect($this->contentContainer->createUrl('/xcoin/overview'),302);
        }

        if (!$account = Account::findOne(['id' => $id])) {
            throw new HttpException(404, 'Account Not found.');
        }

        $account->disable();

        $this->redirect($this->contentContainer->createUrl('/xcoin/overview'));
    }
}
