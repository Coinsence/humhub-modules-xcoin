<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\space\helpers\MembershipHelper;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\ChallengeHelper;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\Funding;
use humhub\components\Controller;
use humhub\modules\xcoin\models\FundingFilter;
use humhub\modules\xcoin\widgets\ChallengeImage;
use Yii;
use yii\db\Expression;

class FundingOverviewController extends Controller
{
    public function actionIndex($challengeId = null)
    {
        $query = Funding::find();
        $query->where(['>', 'xcoin_funding.amount', 0]);
        $query->andWhere(['=', 'xcoin_funding.status', 0]); // only not investment accepted campaigns
        $query->andWhere(['IS NOT', 'xcoin_funding.id', new Expression('NULL')]);
        $query->orderBy(['created_at' => SORT_DESC]);
        $query->andWhere(['review_status' => Funding::FUNDING_REVIEWED]);


        $model = new FundingFilter();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->space_id)
                $query->andWhere(['space_id' => $model->space_id]);
            if ($model->country)
                $query->andWhere(['country' => $model->country]);
            if ($model->city)
                $query->andWhere(['like', 'city', $model->city . '%', false]);
            if ($model->keywords)
                $query->andWhere(['like', 'title', '%' . $model->keywords . '%', false]);

        } else if ($challengeId) {
            $query->andWhere(['challenge_id' => $challengeId]);
        }


        $spacesList = [];
        $challengesList = [];
        $countriesList = [];

        $spaces = Space::find();

        foreach ($spaces->all() as $space) {
            $spacesList[$space->id] = SpaceImage::widget(['space' => $space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $space->name;
        }

        return $this->render('index', [
            'model' => $model,
            'spacesList' => $spacesList,
            'challengesList' => $challengesList,
            'countriesList' => $countriesList,
            'fundings' => $query->all(),
            'challengesCarousel' => ChallengeHelper::getRandomChallenges()
        ]);
    }


    public function actionNew()
    {
        $challenges = Challenge::find()->all();
        if (empty($challenges)) {
            $this->view->info(Yii::t('XcoinModule.funding', 'In order to create a project, there must be running challenges.'));

            return $this->htmlRedirect('/xcoin/funding-overview');
        }

        $user = Yii::$app->user->identity;

        $model = new Funding();
        $model->created_by = $user->id;
        $model->scenario = Funding::SCENARIO_NEW;
        $model->review_status = Funding::FUNDING_REVIEWED;

        if (empty(Yii::$app->request->post('step'))) {

            $spaces = MembershipHelper::getOwnSpaces($user);

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

            $challengesList = [];

            foreach ($challenges as $challenge) {
                if (AssetHelper::getSpaceAsset($model->space)->id != $challenge->asset_id)
                    $challengesList[$challenge->id] = ChallengeImage::widget(['challenge' => $challenge, 'width' => 16, 'link' => true]);
            }

            return $this->renderAjax('../funding/create', [
                    'model' => $model,
                    'challengesList' => $challengesList
                ]
            );
        }

        // Try Save Step 2
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '2') {

            // Step 3: Gallery
            return $this->renderAjax('../funding/media', ['model' => $model]);
        }

        // Try Save Step 3
        if (
            Yii::$app->request->isPost &&
            Yii::$app->request->post('step') == '3'
            && $model->isNameUnique()
            && $model->save()
        ) {
            $model->fileManager->attach(Yii::$app->request->post('fileList'));

            $this->view->saved();

            return $this->redirect($model->space->createUrl('/xcoin/funding/overview', [
                'container' => $model->space,
                'fundingId' => $model->id
            ]));
        }

        // Check validation
        if ($model->hasErrors() && $model->isSecondStep()) {

            return $this->renderAjax('../funding/details', [
                'model' => $model,
                'myAsset' => $model->space ? AssetHelper::getSpaceAsset($model->space) : null
            ]);
        }

        // Step 2: Details
        return $this->renderAjax('../funding/details', [
            'model' => $model,
            'myAsset' => $model->space ? AssetHelper::getSpaceAsset($model->space) : null
        ]);
    }
}
