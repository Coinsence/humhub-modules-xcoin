<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\file\models\File;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\ChallengeHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\Funding;
use humhub\components\Controller;
use humhub\modules\xcoin\models\FundingFilter;
use humhub\modules\xcoin\utils\ImageUtils;
use humhub\modules\xcoin\widgets\ChallengeImage;
use Yii;
use yii\db\Expression;
use yii\web\HttpException;

class FundingOverviewController extends Controller
{

    public function getAccessRules()
    {
        return [
            ['login']
        ];
    }

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

    public function actionIndex($challengeId = null)
    {
        $query = Funding::find();
        $query->where(['>', 'xcoin_funding.amount', 0]);
        $query->andWhere(['or',
            ['xcoin_funding.status' => Funding::FUNDING_STATUS_IN_PROGRESS],
            ['xcoin_funding.status' => Funding::FUNDING_STATUS_INVESTMENT_RESTARTED]
        ]); // only not investment accepted campaigns
        $query->andWhere(['IS NOT', 'xcoin_funding.id', new Expression('NULL')]);
        $query->orderBy(['created_at' => SORT_DESC]);
        $query->andWhere(['or',
            ['xcoin_funding.review_status' => Funding::FUNDING_LAUNCHING_SOON],
            ['xcoin_funding.review_status' => Funding::FUNDING_REVIEWED]
        ]);
        $query->innerJoin('xcoin_challenge', 'xcoin_funding.challenge_id = xcoin_challenge.id');
        $query->andWhere('xcoin_challenge.status = 1');
        $query->andWhere('xcoin_challenge.stopped = 0');

        $model = new FundingFilter();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->asset_id)
                $query->andWhere(['xcoin_challenge.asset_id' => $model->asset_id]);
            if ($model->categories) {
                $query->joinWith('categories category');
                $query->andWhere(['category.id' => $model->categories]);
            }

            if ($model->country)
                $query->andWhere(['country' => $model->country]);
            if ($model->city)
                $query->andWhere(['like', 'city', $model->city . '%', false]);
            if ($model->keywords)
                $query->andWhere(['like', 'xcoin_funding.title', '%' . $model->keywords . '%', false]);

        } else if ($challengeId) {
            $query->andWhere(['challenge_id' => $challengeId]);
        }

        if ($challengeId) {
            $challengesList = Challenge::findAll(['id' => $challengeId]);
        } else {
            $challengesList = Challenge::findAll(['status' => Challenge::CHALLENGE_STATUS_ENABLED, 'stopped' => Challenge::CHALLENGE_ACTIVE]);
        }

        $assetsList = [];
        $countriesList = [];

        foreach ($challengesList as $challenge) {
            $asset = $challenge->asset;
            $space = $challenge->asset->space;
            $assetsList[$asset->id] = SpaceImage::widget(['space' => $space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $space->name;
        }

        $challenge = Challenge::findOne(['id' => $challengeId]);

        return $this->render('index', [
            'selectedChallenge' => $challenge,
            'model' => $model,
            'assetsList' => $assetsList,
            'countriesList' => $countriesList,
            'fundings' => $query->all(),
            'challengesCarousel' => ChallengeHelper::getRandomChallenges()
        ]);
    }


    public function actionNew()
    {
        /** @var Challenge[] $challenges */
        $challenges = Challenge::find()->all();
        if (empty($challenges)) {
            $this->view->info(Yii::t('XcoinModule.funding', 'In order to create a project, there must be running challenges.'));

            return $this->htmlRedirect('/xcoin/funding-overview');
        }

        $user = Yii::$app->user->identity;

        $model = new Funding();
        $model->created_by = $user->id;
        $model->scenario = Funding::SCENARIO_NEW;

        $spaces = SpaceHelper::getSubmitterSpaces($user);

        if (empty(Yii::$app->request->post('step')) && empty(Yii::$app->request->post('overview')) && !empty($spaces)) {

            $spacesList = [];
            foreach ($spaces as $space) {
                if (AssetHelper::getSpaceAsset($space))
                    $spacesList[$space->id] = SpaceImage::widget(['space' => $space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $space->name;
            }

            return $this->renderAjax('../funding/spaces-list', [
                'funding' => $model,
                'spacesList' => $spacesList,
            ]);
        }

        $model->load(Yii::$app->request->post());

        // Step 1: Choose challenge
        if ($model->isFirstStep()) {

            $challengesList = $fundings = [];

            foreach ($challenges as $challenge) {
                if ($challenge->isStopped() or $challenge->isDisabled())
                    continue;

                if ($model->space) {
                    if (AssetHelper::getSpaceAsset($model->space)->id != $challenge->asset_id)
                        $challengesList[$challenge->id] = ChallengeImage::widget(['challenge' => $challenge, 'width' => 16, 'link' => true]);
                } else {
                    $challengesList[$challenge->id] = ChallengeImage::widget(['challenge' => $challenge, 'width' => 16, 'link' => true]);
                }
            }

            foreach (Funding::findAll(['created_by' => $user->id]) as $funding) {
                $fundings[$funding->id] = $funding->title;
            }

            return $this->renderAjax('../funding/create', [
                    'model' => $model,
                    'challengesList' => $challengesList,
                    'fundings' => $fundings
                ]
            );
        }

        // Try Save Step 2
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '4') {

            if ($model->challenge->isStopped()) {
                throw new HttpException(403, 'You can`t submit a funding to a stopped challenge!');
            }

            if ($model->space && !SpaceHelper::canSubmitProject($model->space)) {
                throw new HttpException(401);
            }

            // Step 3: Gallery
            return $this->renderAjax('../funding/media', [
                'model' => $model,
                'lastStepEnabled' => $model->challenge->acceptSpecificRewardingAsset(),
            ]);
        }

        // Try Save Step 3
        if (
            Yii::$app->request->isPost &&
            Yii::$app->request->post('step') == '5'
            && $model->isNameUnique() &&
            $model->validate()
        ) {

            $imageValidation = ImageUtils::checkImageSize(Yii::$app->request->post('fileList'));
            if ($imageValidation == false) {
                return $this->renderAjax('../funding/details', [
                    'model' => $model,
                    'myAsset' => $model->space ? AssetHelper::getSpaceAsset($model->space) : null,
                    'imageError' => "Image size cannot be more than 500 kb"
                ]);
            }

            $model->save();

            if (null !== Yii::$app->request->post('fileList')) {
                $model->fileManager->attach(Yii::$app->request->post('fileList'));

            } elseif (!empty($model->clone_id) && null !== $file = File::findOne(['guid' => $model->picture_file_guid])) {
                $file->object_id = $model->id;
                $file->save();
            }

            $this->view->saved();

            return $this->renderAjax('../funding/funding-overview', [
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

        if (Yii::$app->request->isPost && Yii::$app->request->post('overview') == '1') {
            $funding = Funding::find()->where(['space_id' => $model->space->id, 'title' => $model->title, 'challenge_id' => $model->challenge_id])->one();
            return $this->redirect($model->space->createUrl('funding/overview', [
                'container' => $model->space,
                'fundingId' => $funding->id,
            ]));
        }

        if (!empty($model->clone_id) && null !== $clone = Funding::findOne(['id' => $model->clone_id, 'created_by' => $user->id])) {
            $model->cloneFunding($clone);
        }

        // Step 2: Details
        return $this->renderAjax('../funding/details', [
            'model' => $model,
            'myAsset' => $model->space ? AssetHelper::getSpaceAsset($model->space) : null,
            'imageError' => null
        ]);
    }
}
