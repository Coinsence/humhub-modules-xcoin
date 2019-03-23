<?php

namespace humhub\modules\xcoin\controllers;

use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\FundingInvest;

/**
 * Description of AccountController
 *
 * @author Luke
 */
class FundingController extends ContentContainerController
{

    /**
     * @inheritdoc
     */
    public $validContentContainerClasses = [Space::class];

    public function actionIndex()
    {
        AssetHelper::initContentContainer($this->contentContainer);
        AccountHelper::initContentContainer($this->contentContainer);

        $fundings = Funding::find()->where(['space_id' => $this->contentContainer->id])->all();
        $activeFundings = Funding::find()->where(['space_id' => $this->contentContainer->id])->andWhere(['>', 'available_amount', 0])->all();

        return $this->render('index', [
            'fundings' => $fundings,
            'myAsset' => AssetHelper::getSpaceAsset($this->contentContainer),
            'activeFundings' => $activeFundings,
        ]);
    }

    /**
     * @param $fundingId
     * @return string
     * @throws \Throwable
     * @throws \yii\web\HttpException
     */
    public function actionInvest($fundingId)
    {
        $funding = Funding::findOne(['id' => $fundingId, 'space_id' => $this->contentContainer->id]);
        if ($funding === null) {
            throw new \yii\web\HttpException(404, 'Funding not found!');
        }

        $fromAccount = Account::findOne(['id' => Yii::$app->request->get('accountId')]);
        if ($fromAccount === null) {
            return $this->renderAjax('@xcoin/views/transaction/select-account', [
                'contentContainer' => Yii::$app->user->getIdentity(),
                'nextRoute' => ['/xcoin/funding/invest', 'fundingId' => $funding->id, 'container' => $this->contentContainer],
            ]);
        }

        $model = new FundingInvest();
        $model->fromAccount = $fromAccount;
        $model->funding = $funding;
        $model->amountPay = 1;
        if ($model->load(Yii::$app->request->post()) && $model->invest()) {
            return $this->htmlRedirect(['/xcoin/funding', 'container' => $this->contentContainer]);
        }

        return $this->renderAjax('invest', [
            'funding' => $funding,
            'model' => $model,
            'fromAccount' => $fromAccount
        ]);
    }


    public function actionEdit()
    {
        if (!AssetHelper::canManageAssets($this->contentContainer)) {
            throw new \yii\web\HttpException(401);
        }

        $model = Funding::findOne(['id' => Yii::$app->request->get('id'), 'space_id' => $this->contentContainer->id]);
        if ($model === null) {
            $model = new Funding();
            $model->space_id = $this->contentContainer->id;
            $model->created_by = Yii::$app->user->id;
        }

        $model->scenario = Funding::SCENARIO_EDIT;
        $model->load(Yii::$app->request->post());

        // Step 1: Wanted Asset Selection
        if ($model->isFirstStep()) {
            $assetList = [];
            foreach (Asset::find()->andWhere(['!=', 'id', AssetHelper::getSpaceAsset($this->contentContainer)->id])->all() as $asset) {
                $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $asset->space->name;
            }

            return $this->renderAjax('create', ['model' => $model, 'assetList' => $assetList]);
        }

        // Try Save Step 2
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '2') {

            // Step 3: Details
            return $this->renderAjax('details', ['model' => $model]);
        }

        // Try Save Step 3
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '3') {

            // Step 4: Gallery
            return $this->renderAjax('media', ['model' => $model]);
        }

        // Try Save Step 4
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '4' && $model->save()) {
            $model->fileManager->attach(Yii::$app->request->post('fileList'));

            $this->view->saved();
            return $this->htmlRedirect(['/xcoin/funding', 'container' => $this->contentContainer]);
        }

        // Check validation
        if ($model->hasErrors() && $model->isThirdStep()) {

            return $this->renderAjax('details', ['model' => $model]);
        }

        // Step 2: Exchange Rate
        return $this->renderAjax('edit', [
            'model' => $model,
            'myAsset' => AssetHelper::getSpaceAsset($this->contentContainer),
            'fundingAccountBalance' => AccountHelper::getFundingAccountBalance($this->contentContainer),
        ]);
    }

    public function actionDelete($id)
    {
        if (!AssetHelper::canManageAssets($this->contentContainer)) {
            throw new \yii\web\HttpException(401);
        }

        $model = Funding::findOne(['space_id' => $this->contentContainer->id, 'id' => $id]);
        $model->delete();

        return $this->htmlRedirect(['index', 'container' => $this->contentContainer]);
    }

    public function actionTest()
    {
        print "hello";
    }

}
