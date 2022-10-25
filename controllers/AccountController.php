<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\user\models\User;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\AccountVoucher;
use humhub\modules\xcoin\models\Transaction;
use humhub\modules\xcoin\models\Voucher;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\space\models\Space;
use yii\web\HttpException;
use humhub\modules\space\widgets\Image as SpaceImage;

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
        if ($this->contentContainer instanceof User) {
            $this->redirect($this->contentContainer->createUrl('/xcoin/overview'), 302);
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
        if ($this->contentContainer instanceof User) {
            $this->redirect($this->contentContainer->createUrl('/xcoin/overview'), 302);
        }

        if (!$account = Account::findOne(['id' => $id])) {
            throw new HttpException(404, 'Account Not found.');
        }

        $account->disable();

        $this->redirect($this->contentContainer->createUrl('/xcoin/overview'));
    }

    public function actionVouchers($id)
    {
        $account = Account::findOne(['id' => $id]);

        if ($account === null) {
            throw new HttpException(404);
        }
        if (!AccountHelper::canManageAccount($account)) {
            throw new HttpException(404);
        }
        return $this->render('vouchers', ['account' => $account, 'allowDirectCoinTransfer' => false]);

    }

    public function actionCreateVoucher($accountId)
    {
        $account = Account::findOne(['id' => $accountId]);


        if ($account === null) {
            throw new HttpException(404);
        }
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

        if (empty($accountAssetList)) {
            throw new HttpException(404, 'No assets available on this account!');
        }
        $accountVoucher = new AccountVoucher();
        $accountVoucher->load(Yii::$app->request->post());
        if (Yii::$app->request->isPost) {
            $number = 1;
            if (isset($_POST['number'])) {
                $number = (int)$_POST['number'];
            }
            $i = 1;
            while ($i <= $number) {
                $voucher = new AccountVoucher();
                $voucher->account_id = $accountId;
                $voucher->amount = $accountVoucher->amount;
                $voucher->status = AccountVoucher::STATUS_READY;
                $voucher->value = $this->generateRandomString();
                $voucher->asset_id = $accountVoucher->asset_id;
                $voucher->tag = $accountVoucher->tag;
                $voucher->save();
                $i++;
            }
            return $this->htmlRedirect(['/xcoin/overview', 'container' => $this->contentContainer]);


        }
        return $this->renderAjax('voucher-create', ['accountAssetList' => $accountAssetList,
            'voucher' => $accountVoucher]);
    }

    public function actionRedeemVoucher($accountId)
    {
        $account = Account::findOne(['id' => $accountId]);


        if ($account === null) {
            throw new HttpException(404);
        }

        $model = new AccountVoucher();
        $model->load(Yii::$app->request->post());
        if (Yii::$app->request->isPost) {
            $voucherToRedeem = AccountVoucher::findOne(['value' => $model->value]);
            if (!$voucherToRedeem) {
                $model->addError('value', 'this voucher does not exist');
                return $this->renderAjax('voucher-redeem', [
                    'model' => $model]);
            }
            if ($voucherToRedeem && $voucherToRedeem->status == AccountVoucher::STATUS_USED) {
                $model->addError('value', 'Voucher already used');
                return $this->renderAjax('voucher-redeem', [
                    'model' => $model]);
            }
            if ($voucherToRedeem && $voucherToRedeem->status == AccountVoucher::STATUS_DISABLED) {
                $model->addError('value', 'Voucher Disabled');
                return $this->renderAjax('voucher-redeem', [
                    'model' => $model]);
            }
            $transaction = new Transaction();
            $transaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
            $transaction->from_account_id = $voucherToRedeem->account_id;
            $transaction->to_account_id = $accountId;
            $transaction->asset_id = $voucherToRedeem->asset_id;
            $transaction->amount = $voucherToRedeem->amount;
            $transaction->save();
            $voucherToRedeem->status = AccountVoucher::STATUS_USED;
            $voucherToRedeem->redeemed_account_id = $transaction->to_account_id;
            $voucherToRedeem->save();
            return $this->htmlRedirect(['/xcoin/overview', 'container' => $this->contentContainer]);

        }


        return $this->renderAjax('voucher-redeem', [
            'model' => $model]);
    }


    function generateRandomString($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function actionEnableVoucher($voucherId, $accountId)
    {
        $account = Account::findOne(['id' => $accountId]);
        $voucher = AccountVoucher::findOne(['id' => $voucherId]);


        if ($account === null || $voucher === null) {
            throw new HttpException(404);
        }
        if ($voucher->isVoucherReady()) {
            $voucher->updateAttributes(['status' => AccountVoucher::STATUS_DISABLED]);
        } else {
            $voucher->updateAttributes(['status' => AccountVoucher::STATUS_READY]);
        }
        return $this->render('vouchers', ['account' => $account, 'allowDirectCoinTransfer' => false]);

    }


}
