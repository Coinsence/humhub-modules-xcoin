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
            'projectPlaces' => ProjectplaceRepository::findAllForSpace($currentSpace)
        ]);
    }

    public function actionCreate()
    {
        /** @var Space $currentSpace */
        $currentSpace = $this->contentContainer;

        if (!AssetHelper::canManageAssets($currentSpace)) {
            throw new HttpException(401);
        }

        $model = ProjectplaceFactory::createNewForSpace($currentSpace);

        $assets = AssetHelper::getAllAssets();
        $defaultAsset = AssetHelper::getDefaultAsset();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->htmlRedirect($currentSpace->createUrl('/xcoin/projectplace/index', [
                'projectplaceId' => $model->id
            ]));
        }

        return $this->renderAjax('create', [
                'model' => $model,
                'assets' => $assets,
                'defaultAsset' => $defaultAsset,
            ]
        );
    }
}
