<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2022 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\factories\ProjectplaceFactory;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Projectplace;
use humhub\modules\xcoin\repositories\ProjectplaceRepository;
use Yii;
use yii\web\HttpException;

class ProjectplaceController extends ContentContainerController
{
    public $requireContainer = true;

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

    public $validContentContainerClasses = [Space::class];

    public function actionIndex()
    {
        /** @var Space $currentSpace */
        $currentSpace = $this->contentContainer;

        return $this->render('index', [
            'projectplaces' => ProjectplaceRepository::findAllForSpace($currentSpace)
        ]);
    }

    public function actionOverview($projectplaceId)
    {
        $projectplace = Projectplace::findOne(['id' => $projectplaceId]);

        if (null === $projectplace) {
            throw new HttpException(404);
        }

        return $this->render('overview', [
            'projectplace' => $projectplace
        ]);
    }

    public function actionForm($projectplaceId = null)
    {
        /** @var Space $currentSpace */
        $currentSpace = $this->contentContainer;

        if (!AssetHelper::canManageAssets($currentSpace)) {
            throw new HttpException(401);
        }

        if (null !== $projectplaceId) {
            if (null === $projectplace = Projectplace::findOne(['id' => $projectplaceId])) {
                throw new HttpException(404, Yii::t('AdminModule.controllers_ChallengeController', 'Challenge not found!'));
            }

            $projectplace->setScenario(Projectplace::SCENARIO_UPDATE);
        } else {
            $projectplace = ProjectplaceFactory::createNewForSpace($currentSpace);
        }

        $assets = AssetHelper::getAllAssets();
        $defaultAsset = AssetHelper::getDefaultAsset();

        if ($projectplace->load(Yii::$app->request->post()) && $projectplace->save()) {
            return $this->htmlRedirect($currentSpace->createUrl('/xcoin/projectplace/overview', [
                'projectplaceId' => $projectplace->id
            ]));
        }

        return $this->renderAjax('form', [
            'projectplace' => $projectplace,
            'assets' => $assets,
            'defaultAsset' => $defaultAsset,
            'isCreateForm' => $projectplace->isNewRecord
        ]);
    }
}
