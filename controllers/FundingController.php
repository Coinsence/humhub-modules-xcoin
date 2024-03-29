<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\file\models\File;
use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\AccountBalance;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\ChallengeContactButton;
use humhub\modules\xcoin\models\Transaction;
use humhub\modules\xcoin\utils\ImageUtils;
use Throwable;
use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\FundingInvest;
use yii\base\Model;
use yii\rbac\Item;
use yii\web\HttpException;
use Exception;
use yii\web\UploadedFile;

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
        $callback = Yii::$app->request->get('res');
        $key = Yii::$app->request->get('key');

        if ($callback === 'success' && !empty($key)) {
            Transaction::copyTransaction($key);
        }

        $funding = Funding::findOne(['id' => $fundingId]);

        if (!$funding) {
            throw new HttpException(404);
        }

        return $this->render('overview', [
            'funding' => $funding,
            'contactButtons' => ChallengeContactButton::findAll(['challenge_id' => $funding->challenge_id])]);
    }

    /**
     * @param $fundingId
     * @return string
     * @throws Throwable
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
                'requireAsset' => $funding->challenge->asset,
                'nextRoute' => ['/xcoin/funding/invest', 'fundingId' => $funding->id, 'container' => $this->contentContainer],
            ]);
        }

        if (Yii::$app->request->isPost && Yii::$app->request->post('overview') == '1') {
            return $this->htmlRedirect([
                '/xcoin/account',
                'id' => $fromAccount->id,
                'container' => Yii::$app->user->getIdentity()
            ]);
        }

        $model = new FundingInvest();
        $model->fromAccount = $fromAccount;
        $model->funding = $funding;
        $model->amountPay = 1;
        if ($model->load(Yii::$app->request->post()) && $model->invest()) {
            return $this->renderAjax('invest-overview', [
                'amountPay' => $model->amountPay,
                'amountBuy' => $model->getBuyAmount(),
                'payAsset' => $model->getPayAsset(),
                'buyAsset' => $model->getBuyAsset(),
                'transaction' => $model->payTransaction
            ]);
        }

        return $this->renderAjax('invest', [
            'funding' => $funding,
            'model' => $model,
            'fromAccount' => $fromAccount
        ]);
    }

    public function actionNew($challengeId)
    {
        if (!$challenge = Challenge::findOne(['id' => $challengeId])) {
            throw new HttpException(404, 'Challenge not found!');
        }

        if ($challenge->isStopped()) {
            throw new HttpException(403, 'You can`t submit a funding to a stopped challenge!');
        }

        /** @var Space $currentSpace */
        $currentSpace = $this->contentContainer;
        $user = Yii::$app->user->identity;

        $model = new Funding();
        $model->created_by = $user->id;
        $model->challenge_id = $challenge->id;
        $model->scenario = Funding::SCENARIO_NEW;

        $spaces = SpaceHelper::getSubmitterSpaces($user);

        if (empty(Yii::$app->request->post('step')) && empty(Yii::$app->request->post('overview')) && !empty($spaces)) {

            $spacesList = [];
            foreach ($spaces as $space) {
                if (SpaceHelper::canSubmitProject($space))
                    if (SpaceHelper::canAddSpaceToListForProject($challenge, $space)) {
                        $spacesList[$space->id] = SpaceImage::widget(['space' => $space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $space->name;
                    } elseif ($challenge->acceptSpecificRewardingAsset() || $challenge->acceptNoRewarding()) {
                        $spacesList[$space->id] = SpaceImage::widget(['space' => $space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $space->name;
                    }
            }

            return $this->renderAjax('spaces-list', [
                'funding' => $model,
                'spacesList' => $spacesList,
            ]);
        }

        $model->load(Yii::$app->request->post());

        $fundings = [];

        foreach (Funding::findAll(['created_by' => $user->id]) as $funding) {
            $fundings[$funding->id] = $funding->title;
        }

        if (Yii::$app->request->isPost && !empty($fundings) && Yii::$app->request->post('step') == '1') {
            return $this->renderAjax('../funding/clone', [
                'model' => $model,
                'fundings' => $fundings,
            ]);
        }

        // Try Save Step 4
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '4') {

            if ($model->space && !SpaceHelper::canSubmitProject($model->space)) {
                throw new HttpException(401);
            }
            // Step 3: Gallery
            return $this->renderAjax('media', [
                'model' => $model,
                'lastStepEnabled' => $challenge->acceptSpecificRewardingAsset(),
                'imageError' => null
            ]);
        }

        // Try Save Step 5
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '5' && $model->save()) {
            $imageValidation = ImageUtils::checkImageSize(Yii::$app->request->post('fileList'));
            if ($imageValidation == false) {
                return $this->renderAjax('details', [
                    'model' => $model,
                    'myAsset' => AssetHelper::getSpaceAsset($currentSpace),
                    'imageError' => "Image size cannot be more than 500 kb"

                ]);
            }

            if (null !== Yii::$app->request->post('fileList')) {
                $model->fileManager->attach(Yii::$app->request->post('fileList'));

            } elseif (!empty($model->clone_id) && null !== $file = File::findOne(['guid' => $model->picture_file_guid])) {
                $file->object_id = $model->id;
                $file->save();
            }

            $this->view->saved();

            if ($challenge->acceptSpecificRewardingAsset()) {
                return $this->renderAjax('add-specific-account', [
                    'model' => $model,
                    'nextRoute' => ['/xcoin/funding/allocate', 'fundingId' => $model->id, 'container' => $this->contentContainer],
                    'contentContainer' => $user,
                    'requiredAsset' => AssetHelper::getChallengeSpecificRewardAsset($challenge->specific_reward_asset_id),
                    'spaceId' => $challenge->space_id,
                ]);
            }

            return $this->renderAjax('funding-overview', [
                'model' => $model,
            ]);
        }

        // Check validation
        if ($model->hasErrors() && $model->isSecondStep()) {
            return $this->renderAjax('../funding/details', [
                'model' => $model,
                'myAsset' => $model->space ? AssetHelper::getSpaceAsset($model->space) : null,
                'imageError' => null
            ]);
        }

        //try save step 5
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '5' && $model->save()) {
            return $this->redirect($model->space->createUrl('/xcoin/funding/overview', [
                'container' => $model->space,
                'fundingId' => $model->id,
            ]));
        }

        if (Yii::$app->request->isPost && Yii::$app->request->post('overview') == '1') {
            $funding = Funding::find()->where(['space_id' => $model->space->id,'title'=>$model->title,'challenge_id'=>$model->challenge_id])->one();
            return $this->redirect($model->space->createUrl('/xcoin/funding/overview', [
                'container' => $model->space,
                'fundingId' => $funding->id,
            ]));
        }

        if (!empty($model->clone_id) && !$model->hasErrors() && null !== $clone = Funding::findOne(['id' => $model->clone_id, 'created_by' => $user->id])) {
            $model->cloneFunding($clone);
        }

        // Step 3: Details
        return $this->renderAjax('details', [
            'model' => $model,
            'myAsset' => AssetHelper::getSpaceAsset($currentSpace),
            'imageError'=>null
        ]);
    }

    public function actionEdit()
    {
        /** @var Space $currentSpace */
        $currentSpace = $this->contentContainer;

        if (!AssetHelper::canManageAssets($currentSpace)) {
            throw new HttpException(401);
        }

        if (!$model = Funding::findOne(['id' => Yii::$app->request->get('id'), 'space_id' => $currentSpace->id])) {
            throw new HttpException(404, 'Funding not found!');
        }

        $model->scenario = Funding::SCENARIO_EDIT;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $imageValidation = ImageUtils::checkImageSize(Yii::$app->request->post('fileList'));
            if ($imageValidation == false) {
                return $this->renderAjax('edit', [
                    'model' => $model,
                    'myAsset' => AssetHelper::getSpaceAsset($currentSpace),
                    'imageError' => "Image size cannot be more than 500 kb"

                ]);
            }
            $model->fileManager->attach(Yii::$app->request->post('fileList'));

            $this->view->saved();

            return $this->redirect($currentSpace->createUrl('/xcoin/funding/overview', [
                'container' => $currentSpace,
                'fundingId' => $model->id
            ]));
        }

        return $this->renderAjax('edit', [
            'model' => $model,
            'myAsset' => AssetHelper::getSpaceAsset($currentSpace),
            'imageError' => null

        ]);
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
        $model = Funding::findOne(['id' => $id]);

        if (!SpaceHelper::canReviewProject($model->challenge->space) && !PublicOffersHelper::canReviewSubmittedProjects()) {
            throw new HttpException(401);
        }

        $model->scenario = Funding::SCENARIO_EDIT;
        $model->review_status = $status;

        $model->save();

        $this->view->saved();

        return $this->redirect($this->contentContainer->createUrl('/xcoin/funding/overview', [
            'container' => $this->contentContainer,
            'fundingId' => $model->id
        ]));
    }

    /**
     * @param $fundingId
     * @return string
     * @throws HttpException
     */
    public function actionDetails($fundingId)
    {
        $funding = Funding::findOne(['id' => $fundingId]);

        if (!$funding) {
            throw new HttpException(404);
        }

        return $this->renderAjax('details_popup', [
            'funding' => $funding,
            'contactButtons' => ChallengeContactButton::findAll(['challenge_id' => $funding->challenge_id])
        ]);
    }

    public function actionAccept($id)
    {
        if (!AssetHelper::canManageAssets($this->contentContainer)) {
            throw new HttpException(401);
        }

        $model = Funding::findOne(['space_id' => $this->contentContainer->id, 'id' => $id]);
        $model->updateAttributes(['status' => Funding::FUNDING_STATUS_INVESTMENT_ACCEPTED]);

        return $this->htmlRedirect(['overview',
            'container' => $this->contentContainer,
            'fundingId' => $model->id
        ]);
    }


    public function actionRestart($id)
    {
        if (!AssetHelper::canManageAssets($this->contentContainer)) {
            throw new HttpException(401);
        }

        $model = Funding::findOne(['space_id' => $this->contentContainer->id, 'id' => $id]);
        $model->updateAttributes(['status' => Funding::FUNDING_STATUS_INVESTMENT_RESTARTED]);

        return $this->htmlRedirect(['overview',
            'container' => $this->contentContainer,
            'fundingId' => $model->id
        ]);
    }

    public function actionAllocate($accountId, $fundingId)
    {
        $funding = Funding::findOne(['id' => $fundingId]);
        $balance = AccountBalance::findOne(['account_id' => $accountId, 'asset_id' => Asset::findOne(['id' => $funding->challenge->specific_reward_asset_id])->id]);
        $transaction = new Transaction();
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_ALLOCATE;
        $transaction->asset_id = Asset::findOne(['id' => $funding->challenge->specific_reward_asset_id])->id;
        $transaction->from_account_id = $accountId;
        $transaction->to_account_id = Account::findOne(['funding_id' => $fundingId])->id;
        if ($balance->balance - ($funding->amount * $funding->exchange_rate) > 0) {
            $transaction->amount = $funding->amount * $funding->exchange_rate;
        } else {
            $transaction->amount = round($balance->balance, 1);
        }

        if (!$transaction->save()) {
            throw new Exception('Could not create issue transaction for funding account');
        }

        return $this->htmlRedirect(['overview',
            'container' => $funding->space,
            'fundingId' => $fundingId
        ]);
    }

    /**
     * @param $fundingId
     * @param $contactButtonId
     * @return string
     * @throws HttpException
     */
    public function actionContact($fundingId, $contactButtonId)
    {

        $funding = Funding::findOne(['id' => $fundingId, 'space_id' => $this->contentContainer->id]);
        if ($funding === null) {
            throw new HttpException(404, 'Funding not found!');
        }

        $contactButton = ChallengeContactButton::findOne(['id' => $contactButtonId]);
        if ($contactButton === null) {
            throw new HttpException(404, 'Contact Button not found!');
        }

        $model = new CreateMessage();
        $model->title = $contactButton->button_title . " - " . $funding->title;
        if ($contactButton->receiver == "challenge") {
            $model->recipient = User::findOne(['id' => $funding->challenge->created_by])->guid;
        } else {
            $model->recipient = User::findOne(['id' => $funding->created_by])->guid;
        }

        return $this->renderAjax('contact', [
            'funding' => $funding,
            'contactButton' => $contactButton,
            'model' => $model,
        ]);
    }

    public function actionPublish($id)
    {
        if (!AssetHelper::canManageAssets($this->contentContainer)) {
            throw new HttpException(401);
        }

        $model = Funding::findOne(['space_id' => $this->contentContainer->id, 'id' => $id]);

        if ($model->isPublished()) {
            $model->updateAttributes(['published' => Funding::FUNDING_HIDDEN]);
        } else {
            $model->updateAttributes(['published' => Funding::FUNDING_PUBLISHED]);
        }

        return $this->htmlRedirect(['overview',
            'container' => $this->contentContainer,
            'fundingId' => $model->id
        ]);
    }

    public function actionShow($id){
        if (!AssetHelper::canManageAssets($this->contentContainer)) {
            throw new HttpException(401);
        }

        $model = Funding::findOne(['space_id' => $this->contentContainer->id, 'id' => $id]);

        if ($model->isActivated()) {
            $model->updateAttributes(['activate_funding' => Funding::FUNDING_DEACTIVATED]);
        } else {
            $model->updateAttributes(['activate_funding' => Funding::FUNDING_ACTIVATED]);
        }

        return $this->htmlRedirect(['overview',
            'container' => $this->contentContainer,
            'fundingId' => $model->id
        ]);
    }

}
