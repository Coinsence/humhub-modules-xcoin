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
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\ChallengeContactButton;
use humhub\modules\xcoin\models\Funding;
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

        if ($challenge->showUnreviewedSubmissions() || Space::findOne(['id' => $challenge->space_id])->isAdmin(Yii::$app->user->identity)) {
            $fundings = $challenge->getFundings()->all();
        } else {
            $fundings = Funding::findAll(['challenge_id' => $challenge->id, 'review_status' => 1]);
        }

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
                $this->createButton(
                    $model->id,
                    ChallengeContactButton::CONTACT_BUTTON_ENABLED,
                    $_POST['firstButtonTitle'], $_POST['firstButtonText'],
                    $_POST['firstButtonReceiver']);
            } else {
                $this->createButton($model->id);
            }
            if (isset($_POST['secondButton'])) {
                $this->createButton(
                    $model->id,
                    ChallengeContactButton::CONTACT_BUTTON_ENABLED,
                    $_POST['secondButtonTitle'],
                    $_POST['secondButtonText'],
                    $_POST['secondButtonReceiver']);
            } else {
                $this->createButton($model->id);
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

    private function createButton(
        $challengeId,
        $status = ChallengeContactButton::CONTACT_BUTTON_DISABLED,
        $buttonTitle = null,
        $popupText = null,
        $receiver = null
    )
    {
        $button = new ChallengeContactButton();
        $button->challenge_id = $challengeId;
        $button->status = $status;
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
        $contactButtons = $model->getContactButtons()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->fileManager->attach(Yii::$app->request->post('fileList'));
            $this->view->saved();
            if (isset($_POST['firstButton'])) {
                $this->updateButton(
                    $contactButtons[0],
                    $_POST['firstButtonReceiver'],
                    $_POST['firstButtonTitle'],
                    $_POST['firstButtonText'],
                    ChallengeContactButton::CONTACT_BUTTON_ENABLED
                );
            } else {
                $this->disableButton($contactButtons[0]);
            }
            if (isset($_POST['secondButton'])) {
                $this->updateButton(
                    $contactButtons[1],
                    $_POST['secondButtonReceiver'],
                    $_POST['secondButtonTitle'],
                    $_POST['secondButtonText'],
                    ChallengeContactButton::CONTACT_BUTTON_ENABLED
                );
            } else {
                $this->disableButton($contactButtons[1]);
            }
            return $this->htmlRedirect($currentSpace->createUrl('/xcoin/challenge/overview', [
                'challengeId' => $model->id,
            ]));
        }
        return $this->renderAjax('edit', [
                'model' => $model,
                'assets' => $assets,
                'contactButtons' => $contactButtons,
            ]
        );
    }

    private function updateButton(
        ChallengeContactButton $button,
        $receiver, $button_title,
        $popup_text,
        $status
    )
    {
        $button->receiver = $receiver;
        $button->button_title = $button_title;
        $button->popup_text = $popup_text;
        $button->status = $status;
        $button->save();
    }

    private function disableButton(ChallengeContactButton $button)
    {
        $button->status = ChallengeContactButton::CONTACT_BUTTON_DISABLED;
        $button->save();
    }

    public function actionReviewFunding($id, $status)
    {
        $model = Funding::findOne(['id' => $id]);
        if ($model == null) {
            throw new HttpException(404, 'Funding Not found');
        }

        if (!SpaceHelper::canReviewProject($model->challenge->space) && !PublicOffersHelper::canReviewSubmittedProjects()) {
            throw new HttpException(401);
        }

        $model->scenario = Funding::SCENARIO_EDIT;
        $model->review_status = $status;

        $model->save();

        $this->view->saved();

        return $this->redirect($this->contentContainer->createUrl('/xcoin/challenge/overview', [
            'container' => $this->contentContainer,
            'challengeId' => $model->challenge_id
        ]));
    }
}
