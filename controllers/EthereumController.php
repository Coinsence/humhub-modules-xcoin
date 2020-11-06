<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\controllers;

use GuzzleHttp\Exception\GuzzleException;
use humhub\components\Event;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\ethereum\calls\Wallet;
use humhub\modules\space\models\Space;
use humhub\modules\user\components\CheckPasswordValidator;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\models\Account;
use Yii;
use yii\base\DynamicModel;
use yii\web\HttpException;

class EthereumController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (!$this->module->isCrowdfundingEnabled()) {
            throw new HttpException(403, Yii::t('XcoinModule.base', 'Crowdfunding is not enabled'));
        }

        return true;
    }

    const EVENT_ENABLE_ETHEREUM = 'enableEthereum';
    const EVENT_MIGRATE_MISSING_TRANSACTIONS = 'migrateMissingTransactions';
    const EVENT_SYNCHRONIZE_BALANCES = 'synchronizeBalances';

    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        $space = Space::findOne(['id' => $this->contentContainer->id]);

        return $this->render('index', [
            'space' => $space,
        ]);
    }

    public function actionEnable()
    {
        $space = Space::findOne(['id' => $this->contentContainer->id]);

        if ($space->eth_status == Space::ETHEREUM_STATUS_DISABLED) {
            $space->updateAttributes(['eth_status' => Space::ETHEREUM_STATUS_IN_PROGRESS]);

            Event::trigger(self::class, self::EVENT_ENABLE_ETHEREUM, new Event(['sender' => $space]));
        }

        return $this->asJson([
            'success' => true,
        ]);
    }

    public function actionMigrateTransactions()
    {
        $space = Space::findOne(['id' => $this->contentContainer->id]);

        if ($space->eth_status == Space::ETHEREUM_STATUS_ENABLED) {
            Event::trigger(self::class, self::EVENT_MIGRATE_MISSING_TRANSACTIONS, new Event(['sender' => $space]));
        }

        return $this->asJson([
            'success' => true,
        ]);
    }

    public function actionSynchronizeBalances()
    {
        $space = Space::findOne(['id' => $this->contentContainer->id]);

        if ($space->eth_status == Space::ETHEREUM_STATUS_ENABLED) {
            Event::trigger(self::class, self::EVENT_SYNCHRONIZE_BALANCES, new Event(['sender' => $space]));
        }

        return $this->asJson([
            'success' => true,
        ]);
    }

    /**
     * @param $accountId
     * @return string
     * @throws HttpException
     * @throws GuzzleException
     */
    public function actionLoadPrivateKey($accountId)
    {
        $passwordModel = new DynamicModel(['currentPassword']);
        $passwordModel->addRule(['currentPassword'], CheckPasswordValidator::class);
        $passwordModel->addRule(['currentPassword'], 'required');

        if ($passwordModel->load(Yii::$app->request->post()) && $passwordModel->validate()) {

            $account = Account::findOne(['id' => $accountId]);

            if ($account === null) {
                throw new HttpException(404);
            }

            if (!AccountHelper::canManageAccount($account)) {
                throw new HttpException(401);
            }


            return $this->renderAjax('wallet-private-key', [
                'privateKey' => Wallet::getWallet($account)
            ]);
        }

        return $this->renderAjax('password-prompt', [
            'model' => $passwordModel
        ]);

    }
}
