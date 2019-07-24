<?php

namespace humhub\modules\xcoin\controllers;

use humhub\components\Controller;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Product;
use Yii;

class MarketplaceController extends Controller
{

    public function actionIndex($verified = false)
    {
        $products = Product::find()->where([
                'status' => Product::STATUS_AVAILABLE,
                'review_status' => $verified == Product::PRODUCT_REVIEWED ? Product::PRODUCT_REVIEWED : Product::PRODUCT_NOT_REVIEWED
            ])->all();

        return $this->render('index', [
            'products' => $products,
        ]);
    }

    public function actionSell()
    {
        $model = new Product();
        $model->scenario = Product::SCENARIO_CREATE;
        $model->product_type = Product::TYPE_PERSONAL;

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
        foreach (Asset::find()->all() as $asset) {
            if ($asset->getIssuedAmount()) {
                $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $asset->space->name;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $model->fileManager->attach(Yii::$app->request->post('fileList'));
            $this->view->saved();

            return $this->htmlRedirect(['/xcoin/marketplace']);
        }

        return $this->renderAjax('sell', ['model' => $model, 'assetList' => $assetList, 'defaultAsset' => $defaultAsset]);
    }
}
