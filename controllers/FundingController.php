<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\xcoin\helpers\PublicOffersHelper;
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
use yii\web\HttpException;

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

        $fundings = Funding::find()->where(['space_id' => $this->contentContainer->id])->all();

        return $this->render('index', [
            'fundings' => $fundings,
            'myAsset' => AssetHelper::getSpaceAsset($this->contentContainer),
        ]);
    }

    public function actionOverview($fundingId)
    {
        $funding = Funding::findOne(['id' => $fundingId]);

        if(!$funding) {
            throw new HttpException(404);
        }

        return $this->render('overview', [
            'funding' => $funding,
        ]);
    }

    /**
     * @param $fundingId
     * @return string
     * @throws \Throwable
     * @throws HttpException
     */
    public function actionInvest($fundingId)
    {
        $funding = Funding::findOne(['id' => $fundingId, 'space_id' => $this->contentContainer->id]);
        if ($funding === null) {
            throw new HttpException(404, 'Funding not found!');
        }

        $fromAccount = Account::findOne(['id' => Yii::$app->request->get('accountId')]);
        if ($fromAccount === null) {
            return $this->renderAjax('@xcoin/views/transaction/select-account', [
                'contentContainer' => Yii::$app->user->getIdentity(),
                'requireAsset' => $funding->getAsset()->one(),
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
            throw new HttpException(401);
        }

        $model = Funding::findOne(['id' => Yii::$app->request->get('id'), 'space_id' => $this->contentContainer->id]);
        if ($model === null) {
            $model = new Funding();
            $model->space_id = $this->contentContainer->id;
            $model->created_by = Yii::$app->user->id;
        }

        $model->scenario = Funding::SCENARIO_EDIT;
        $model->load(Yii::$app->request->post());

        // Step 1: Wanted Asset Selection and Exchange Rate
        if ($model->isFirstStep()) {

            // Get default Asset that will be preselected
            $defaultAsset = null;

            /* "defaultAssetName" parameter contains the default asset name that must be preselected
            This parameter should be introduced in the file humhub/protected/config/common.php*/
            if (array_key_exists('defaultAssetName', Yii::$app->params)) {
                $defaultAssetName = Yii::$app->params['defaultAssetName'];
                $defaultAssetSpace = Space::findOne(['name' => $defaultAssetName]);

                if ($defaultAssetSpace) {
                    $defaultAsset = AssetHelper::getSpaceAsset($defaultAssetSpace);
                    if (!$defaultAsset->getIssuedAmount())
                        $defaultAsset = null;
                }
            }

            $assetList = [];
            foreach (Asset::find()->andWhere(['!=', 'id', AssetHelper::getSpaceAsset($this->contentContainer)->id])->all() as $asset) {
                $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $asset->space->name;
            }

            return $this->renderAjax('create', [
                    'model' => $model,
                    'assetList' => $assetList,
                    'defaultAsset' => $defaultAsset,
                    'myAsset' => AssetHelper::getSpaceAsset($this->contentContainer),
                ]
            );
        }

        // Try Save Step 2
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '2') {

            // Step 3: Gallery
            return $this->renderAjax('media', ['model' => $model]);
        }

        // Try Save Step 3
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '3' && $model->save()) {
            $model->fileManager->attach(Yii::$app->request->post('fileList'));

            $this->view->saved();

            return $this->redirect($this->contentContainer->createUrl('/xcoin/funding/overview', [
                'container' => $this->contentContainer,
                'fundingId' => $model->id
            ]));
        }

        // Check validation
        if ($model->hasErrors() && $model->isSecondStep()) {

            return $this->renderAjax('details', ['model' => $model]);
        }

        // Step 2: Details
        return $this->renderAjax('details', ['model' => $model]);
    }

    public function actionCancel($id)
    {
        if (!AssetHelper::canManageAssets($this->contentContainer)) {
            throw new HttpException(401);
        }

        $model = Funding::findOne(['space_id' => $this->contentContainer->id, 'id' => $id]);
        $model->delete();

        return $this->htmlRedirect(['index', 'container' => $this->contentContainer]);
    }


    public function actionReview($id, $status)
    {
        if(!PublicOffersHelper::canReviewPublicOffers()){
            throw new HttpException(401);
        }

        $model = Funding::findOne(['space_id' => $this->contentContainer->id, 'id' => $id]);
        $model->scenario = Funding::SCENARIO_EDIT;
        $model->review_status = $status;

        $model->save();

        $this->view->saved();

        return $this->redirect($this->contentContainer->createUrl('/xcoin/funding/overview', [
            'container' => $this->contentContainer,
            'fundingId' => $model->id
        ]));
    }
}
