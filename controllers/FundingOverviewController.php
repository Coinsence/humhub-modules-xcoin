<?php

namespace humhub\modules\xcoin\controllers;

use Colors\RandomColor;
use humhub\modules\space\components\UrlValidator;
use humhub\modules\space\helpers\MembershipHelper;
use humhub\modules\space\models\Space;
use humhub\modules\space\Module;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Funding;
use humhub\components\Controller;
use Yii;
use yii\db\Expression;
use yii\web\HttpException;

class FundingOverviewController extends Controller
{

    public function actionIndex($verified = false)
    {
        $query = Funding::find();
        $query->where(['>', 'xcoin_funding.amount', 0]);
        $query->andWhere(['=', 'xcoin_funding.status', 0]); // only not investment accepted campaigns
        $query->andWhere(['IS NOT', 'xcoin_funding.id', new Expression('NULL')]);
        $query->orderBy(['created_at' => SORT_DESC]);

        if ($verified == Funding::FUNDING_REVIEWED) {
            $query->andWhere(['review_status' => Funding::FUNDING_REVIEWED]);
        } else {
            $query->andWhere(['review_status' => Funding::FUNDING_NOT_REVIEWED]);
        }

        return $this->render('index', ['fundings' => $query->all()]);
    }


    public function actionNew()
    {
        $user = Yii::$app->user->identity;

        $model = new Funding();
        $model->created_by = $user->id;

        $space = null;

        if (empty(Yii::$app->request->post('step'))) {

            $spaces = MembershipHelper::getOwnSpaces($user);

            $spacesList = [];
            foreach ($spaces as $space) {
                $spacesList[$space->id] = SpaceImage::widget(['space' => $space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $space->name;
            }

            return $this->renderAjax('spaces-list', [
                'funding' => $model,
                'spacesList' => $spacesList,
            ]);
        }

        $model->scenario = Funding::SCENARIO_EDIT;
        $model->load(Yii::$app->request->post());


        if (!$model->space_id) {

            /* @var Module $module */
            $module = Yii::$app->getModule('space');

            // init space model
            $space = new Space();
            $space->scenario = Space::SCENARIO_CREATE;
            $space->visibility = $module->settings->get('defaultVisibility', Space::VISIBILITY_REGISTERED_ONLY);
            $space->join_policy = $module->settings->get('defaultJoinPolicy', Space::JOIN_POLICY_APPLICATION);
            $space->color = RandomColor::one(['luminosity' => 'dark']);
            $space->space_type = Space::SPACE_TYPE_FUNDING;
            $space->name = SpaceHelper::generateRandomSpaceName();

            if (!$space->save()) {
                throw new HttpException(400);
            }

            $model->space_id = $space->id;
        }

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
            foreach (Asset::find()->andWhere(['!=', 'id', AssetHelper::getSpaceAsset($model->space)->id])->all() as $asset) {
                $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $asset->space->name;
            }

            return $this->renderAjax('../funding/create', [
                    'model' => $model,
                    'assetList' => $assetList,
                    'defaultAsset' => $defaultAsset,
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

            // update space name & description if automatically created
            if ($model->space->space_type == Space::SPACE_TYPE_FUNDING) {

                $model->space->updateAttributes([
                    'name' => $model->title,
                    'description' => $model->description,
                    'url' => UrlValidator::autogenerateUniqueSpaceUrl($model->title)
                ]);
            }

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
                'myAsset' => AssetHelper::getSpaceAsset($model->space)
            ]);
        }

        // Step 2: Details
        return $this->renderAjax('../funding/details', [
            'model' => $model,
            'myAsset' => AssetHelper::getSpaceAsset($model->space)
        ]);
    }
}
