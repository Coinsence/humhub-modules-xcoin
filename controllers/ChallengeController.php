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
use humhub\modules\xcoin\models\ChallengeContactButton;
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
        $challenges = Challenge::find()
            ->where(['space_id' => $this->contentContainer->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'challenges' => $challenges
        ]);
    }

    /**
     * @param $challengeId
     * @return string
     * @throws HttpException
     */
    public function actionOverview($challengeId)
    {
        $challenge = Challenge::findOne(['id' => $challengeId, 'space_id' => $this->contentContainer]);

        if (!$challenge) {
            throw new HttpException(404);
        }

        $fundings = $challenge->getFundings()->all();

        return $this->render('overview', [
            'challenge' => $challenge,
            'fundings' => $fundings
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
        $assets = AssetHelper::getAllAssets();
        $defaultAsset = AssetHelper::getDefaultAsset();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->fileManager->attach(Yii::$app->request->post('fileList'));
            $this->view->saved();
            if (isset($_POST['firstButton'])) {
                $this->createButton($model->id, $_POST['firstButtonTitle'], $_POST['firstButtonText'], $_POST['firstButtonReceiver']);
            }
            if (isset($_POST['secondButton'])) {
                $this->createButton($model->id, $_POST['secondButtonTitle'], $_POST['secondButtonText'], $_POST['secondButtonReceiver']);
            }
            return $this->htmlRedirect($currentSpace->createUrl('/xcoin/challenge/index', [
                'challengeId' => $model->id
            ]));
        }

        return $this->renderAjax('create', [
                'model' => $model,
                'assets' => $assets,
                'defaultAsset' => $defaultAsset,
            ]
        );
    }

    private function createButton($challengeId, $buttonTitle, $popupText, $receiver)
    {
        $button = new ChallengeContactButton();
        $button->status = true;
        $button->challenge_id = $challengeId;
        $button->receiver = $receiver;
        $button->button_title = $buttonTitle;
        $button->popup_text = $popupText;
        $button->save();
    }

    /**
     * @return string|Response
     * @throws HttpException
     */
    public function actionEdit()
    {
        /** @var Space $currentSpace */
        $currentSpace = $this->contentContainer;

        if (!AssetHelper::canManageAssets($currentSpace)) {
            throw new HttpException(401);
        }

        $model = Challenge::findOne(['id' => Yii::$app->request->get('id')]);

        if ($model == null) {
            throw new HttpException(404, Yii::t('AdminModule.controllers_ChallengeController', 'Challenge not found!'));
        }

        $model->scenario = Challenge::SCENARIO_EDIT;

        $assets = AssetHelper::getAllAssets();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->fileManager->attach(Yii::$app->request->post('fileList'));

            $this->view->saved();

            return $this->htmlRedirect($currentSpace->createUrl('/xcoin/challenge/overview', [
                'challengeId' => $model->id
            ]));
        }

        return $this->renderAjax('edit', [
                'model' => $model,
                'assets' => $assets
            ]
        );
    }
}
