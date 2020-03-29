<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Challenge;
use Yii;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Description of ChallengeController
 */
class ChallengeController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public $validContentContainerClasses = [Space::class];

    public function actionIndex($challengeId = null)
    {
        $displayedChallenge = null;

        if ($challengeId && !$displayedChallenge = Challenge::findOne(['id' => $challengeId])) {
            throw new HttpException(404, 'Challenge not found!');
        }

        $challenges = Challenge::find()
            ->where(['space_id' => $this->contentContainer->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'challenges' => $challenges,
            'displayedChallenge' => $displayedChallenge
        ]);
    }

    /**
     * @return string|Response
     * @throws HttpException
     */
    public function actionCreate()
    {
        /** @var Space $currentSpace */
        $currentSpace = $this->contentContainer;

        if (!AssetHelper::canManageAssets($currentSpace)) {
            throw new HttpException(401);
        }

        $model = new Challenge();
        $model->scenario = Challenge::SCENARIO_CREATE;
        $model->space_id = $this->contentContainer->id;

        $assets = AssetHelper::getAllAssets($currentSpace);
        $defaultAsset = AssetHelper::getDefaultAsset();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->fileManager->attach(Yii::$app->request->post('fileList'));

            $this->view->saved();

            return $this->htmlRedirect(['index', 'challengeId' => $model->id]);
        }

        return $this->renderAjax('create', [
                'model' => $model,
                'assets' => $assets,
                'defaultAsset' => $defaultAsset,
            ]
        );
    }
}
