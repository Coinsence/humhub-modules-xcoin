<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\controllers;

use humhub\libs\Iso3166Codes;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\ChallengeContactButton;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\models\FundingCategory;
use humhub\modules\xcoin\utils\ImageUtils;
use humhub\modules\xcoin\models\ChallengeFundingFilter;
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
        if (Yii::$app->user->isAdmin()) {
            $challenges = Challenge::find()
                ->where(['space_id' => $this->contentContainer->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
        } else {
            $challenges = Challenge::find()
                ->where(['space_id' => $this->contentContainer->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->andWhere(['hidden' => Challenge::CHALLENGE_SHOWN])
                ->all();
        }

        return $this->render('index', [
            'challenges' => $challenges
        ]);
    }

    /**
     * @param $challengeId
     * @return string
     * @throws HttpException
     */
    public function actionOverview($challengeId, $categoryId = null)
    {
        $challenge = Challenge::findOne(['id' => $challengeId, 'space_id' => $this->contentContainer]);

        if (!$challenge) {
            throw new HttpException(404);
        }
        
        $query = Funding::find();

        if ($challenge->showUnreviewedSubmissions() || Space::findOne(['id' => $challenge->space_id])->isAdmin(Yii::$app->user->identity)) {
            $query->where(['challenge_id' => $challenge->id]);
        } else {
            $query->where(['challenge_id' => $challenge->id, 'published' => 1, 'review_status' => [1, 2]]);
        }

        $categories = [];
        foreach ($query->all() as $funding) {
            foreach ($funding->getCategories()->all() as $category) {
                $categories[] = $category;
            }
        }

        if ($challenge->with_location_filter === Challenge::CHALLENGE_LOCATION_FILTER_SHOWN) {
            $model = new ChallengeFundingFilter();

            $locations = [];
            foreach ($query->all() as $funding) {
                $locations[$funding->country . "|" . $funding->city] = $funding->country . ", " . $funding->city;
            }

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->category) {
                    $query->joinWith('categories category');
                    $query->andWhere(['category.id' => [$model->category]]);
                }

                $locations = [];
                foreach ($query->all() as $funding) {
                    $locations[$funding->country . "|" . $funding->city] = iso3166Codes::country($funding->country) . ", " . $funding->city;
                }

                if ($model->location) {
                    [$country, $city] = explode('|', $model->location);
                    $query->andWhere(['country' => $country]);
                    $query->andWhere(['like', 'city', $city . '%', false]);
                }
            }

            return $this->render('overview', [
                'model' => $model,
                'challenge' => $challenge,
                'fundings' => $query->all(),
                'categories' => $categories,
                'locations' => $locations,
            ]);
        } else {
            if ($categoryId) {
                $query->joinWith('categories category');
                $query->andWhere(['category.id' => [$categoryId]]);
            }
    
            return $this->render('overview', [
                'challenge' => $challenge,
                'fundings' => $query->all(),
                'categories' => array_unique($categories, SORT_REGULAR),
                'activeCategory' => $categoryId,
            ]);
        }
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
            $imageValidation = ImageUtils::checkImageSize(Yii::$app->request->post('fileList'));
            if ($imageValidation == false) {
                return $this->renderAjax('create', [
                        'model' => $model,
                        'assets' => $assets,
                        'defaultAsset' => $defaultAsset,
                        'imageError' => "Image size cannot be more than 500 kb"
                    ]
                );
            }
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
                'imageError' => null
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

        if (!$contactButtons) {
            $this->initiateChallengeContactButtons($model->id);
            $contactButtons = $model->getContactButtons()->all();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $imageValidation = ImageUtils::checkImageSize(Yii::$app->request->post('fileList'));
            if ($imageValidation == false) {
                return $this->renderAjax('edit', [
                        'model' => $model,
                        'assets' => $assets,
                        'contactButtons' => $contactButtons,
                        'imageError' => "Image size cannot be more than 500 kb"
                    ]
                );
            }
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
                'imageError' => null
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

    private function initiateChallengeContactButtons($id)
    {
        $this->createButton($id);
        $this->createButton($id);
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

    public function actionHideFundingLocation($id, $status)
    {
        $model = Funding::findOne(['id' => $id]);
        if ($model == null) {
            throw new HttpException(404, 'Funding Not found');
        }

        if (!SpaceHelper::canReviewProject($model->challenge->space) && !PublicOffersHelper::canReviewSubmittedProjects()) {
            throw new HttpException(401);
        }

        $model->scenario = Funding::SCENARIO_EDIT;
        $model->hidden_location = $status;

        $model->save();

        $this->view->saved();

        return $this->redirect($this->contentContainer->createUrl('/xcoin/challenge/overview', [
            'container' => $this->contentContainer,
            'challengeId' => $model->challenge_id
        ]));
    }

    public function actionHideFundingDetails($id, $status)
    {
        $model = Funding::findOne(['id' => $id]);
        if ($model == null) {
            throw new HttpException(404, 'Funding Not found');
        }

        if (!SpaceHelper::canReviewProject($model->challenge->space) && !PublicOffersHelper::canReviewSubmittedProjects()) {
            throw new HttpException(401);
        }

        $model->scenario = Funding::SCENARIO_EDIT;
        $model->hidden_details = $status;

        $model->save();

        $this->view->saved();

        return $this->redirect($this->contentContainer->createUrl('/xcoin/challenge/overview', [
            'container' => $this->contentContainer,
            'challengeId' => $model->challenge_id
        ]));
    }
}
