<?php

namespace humhub\modules\xcoin\controllers;

use Exception;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Marketplace;
use humhub\modules\xcoin\models\Product;
use Throwable;
use Yii;
use yii\web\HttpException;

class ProductController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        if ($this->contentContainer instanceof Space) {
            $products = Product::find()->where(['space_id' => $this->contentContainer->id])->all();

            return $this->render('index', [
                'products' => $products,
            ]);
        } else {
            $products = Product::find()->where([
                'created_by' => $this->contentContainer->id,
                'product_type' => Product::TYPE_PERSONAL
            ])->all();

            return $this->render('index_user', [
                'products' => $products,
            ]);
        }
    }

    public function actionNew($marketplaceId)
    {
        if (!$marketplace = Marketplace::findOne(['id' => $marketplaceId])) {
            throw new HttpException(404, 'Marketplace not found!');
        }

        if ($marketplace->isStopped()) {
            throw new HttpException(403, 'You can`t sell a product in a closed marketplace!');
        }

        $user = Yii::$app->user->identity;

        $model = new Product();
        $model->created_by = $user->id;
        $model->marketplace_id = $marketplace->id;
        $model->scenario = Product::SCENARIO_CREATE;

        $model->load(Yii::$app->request->post());

        // Step 2: Details
        if ($model->isSecondStep()) {

            $spaces = SpaceHelper::getSellerSpaces($user);

            $accountsList = [];

            $accountsList[Product::PRODUCT_USER_DEFAULT_ACCOUNT] = UserImage::widget(['user' => $user, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' Default';

            foreach ($spaces as $space) {
                if (AssetHelper::getSpaceAsset($space))
                    $accountsList[$space->id] = SpaceImage::widget(['space' => $space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $space->name;
            }

            $model->account = Product::PRODUCT_USER_DEFAULT_ACCOUNT;

            if (!Yii::$app->request->isPost) {
                return $this->renderAjax('../product/details', [
                    'model' => $model,
                    'accountsList' => $accountsList
                ]);
            }
        }

        // Step 3: Gallery
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '2') {
            if ($model->marketplace->isStopped()) {
                throw new HttpException(403, 'You can`t sell a product in a closed marketplace!');
            }

            if ($model->space && !SpaceHelper::canSellProduct($model->space)) {
                throw new HttpException(401);
            }

            if ($model->account == Product::PRODUCT_USER_DEFAULT_ACCOUNT) {
                $model->space_id = null;
                $model->product_type = Product::TYPE_PERSONAL;
            } else {
                $model->space_id = $model->account;
                $model->product_type = Product::TYPE_SPACE;
            }

            return $this->renderAjax('../product/media', ['model' => $model]);
        }

        // Try Saving
        if (
            Yii::$app->request->isPost &&
            Yii::$app->request->post('step') == '3' &&
            $model->isNameUnique() &&
            $model->validate() &&
            $model->save()
        ) {
            $model->fileManager->attach(Yii::$app->request->post('fileList'));

            $this->view->saved();

            $url = $model->isSpaceProduct() ?
                $model->space->createUrl('/xcoin/product/overview', [
                    'container' => $model->space,
                    'productId' => $model->id
                ]) :
                $user->createUrl('/xcoin/product/overview', [
                    'container' => $user,
                    'productId' => $model->id
                ]);

            return $this->redirect($url);
        }

        // Check validation
        if ($model->hasErrors() && $model->isSecondStep()) {

            return $this->renderAjax('../product/details', [
                'model' => $model,
                'accountsList' => $accountsList
            ]);

        }
    }

    /**
     * @param $productId
     * @return string
     * @throws HttpException
     */
    public function actionOverview($productId)
    {
        $product = Product::findOne(['id' => $productId]);

        if (!$product) {
            throw new HttpException(404);
        }

        return $this->render('overview', [
            'product' => $product,
        ]);
    }

  

    /**
     * @throws HttpException
     * @throws Exception
     */
    public function actionEdit()
    {
        if ($this->contentContainer instanceof Space) {
            if (!AssetHelper::canManageAssets($this->contentContainer)) {
                throw new HttpException(401);
            }

            $model = Product::findOne(['id' => Yii::$app->request->get('id'), 'space_id' => $this->contentContainer->id]);
        } else {
            $model = Product::findOne(['id' => Yii::$app->request->get('id')]);
            if ($model && !$model->isOwner(Yii::$app->user->identity)) {
                throw new HttpException(401);
            }
        }

        if ($model === null) {
            throw new HttpException(404);
        }


        $model->scenario = Product::SCENARIO_EDIT;
        $model->load(Yii::$app->request->post());

        $assetList = [];
        foreach (Asset::find()->all() as $asset) {
            if ($asset->getIssuedAmount()) {
                $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $asset->space->name;
            }
        }

        if (Yii::$app->request->isPost && $model->save()) {

            $model->fileManager->attach(Yii::$app->request->post('fileList'));
            $this->view->saved();

            return $this->htmlRedirect(['/xcoin/product', 'container' => $this->contentContainer]);
        }

        return $this->renderAjax('edit', ['model' => $model, 'assetList' => $assetList]);
    }

    /**
     * @throws Throwable
     */
    public function actionDelete()
    {
        if ($this->contentContainer instanceof Space) {
            if (!AssetHelper::canManageAssets($this->contentContainer)) {
                throw new HttpException(401);
            }

            $model = Product::findOne(['id' => Yii::$app->request->get('id'), 'space_id' => $this->contentContainer->id]);
        } else {
            $model = Product::findOne(['id' => Yii::$app->request->get('id')]);
            if ($model && !$model->isOwner(Yii::$app->user->identity)) {
                throw new HttpException(401);
            }
        }

        if ($model === null) {
            throw new HttpException(404);
        }

        $model->delete();

        $this->view->saved();

        return $this->htmlRedirect(['/xcoin/product', 'container' => $this->contentContainer]);
    }

    public function actionReview($id, $status)
    {

        $model = Product::findOne(['id' => $id]);

        if (!SpaceHelper::canReviewProject($model->marketplace->space) && !PublicOffersHelper::canReviewSubmittedProjects()) {
            throw new HttpException(401);
        }

        $model->scenario = Product::SCENARIO_EDIT;
        $model->review_status = $status;

        $model->save();

        $this->view->saved();

        return $this->redirect($this->contentContainer->createUrl('/xcoin/product/overview', [
            'container' => $this->contentContainer,
            'productId' => $model->id
        ]));
    }

    /**
     * @param $productId
     * @return string
     * @throws HttpException
     */
    public function actionDetails($productId)
    {
        $product = Product::findOne(['id' => $productId]);

        if (!$product) {
            throw new HttpException(404);
        }

        return $this->renderAjax('details_popup', [
            'product' => $product,
        ]);
    }

    public function actionBuy($productId)
    {
        $product = Product::findOne(['id' => $productId]);

        if (!$product) {
            throw new HttpException(404);
        }

        $message = new Message(['title' => Yii::t('XcoinModule.product', "Sales discussion for : {$product->name}")]);
        $message->save();

        /** @var User $buyer */
        $buyer = $this->contentContainer;

        /** @var User $seller */
        $seller = $product->getCreatedBy()->one();


        $message->addRecepient($seller, true);
        $message->addRecepient($buyer);

        MessageEntry::createForMessage($message, $seller, $product->buy_message)->save();

        // notify the buyer
        try {
            $message->notify($buyer);
        } catch (\Exception $e) {
            Yii::error('Could not send notification e-mail to: ' . $buyer->username . ". Error:" . $e->getMessage());
        }

        $this->view->info(Yii::t('XcoinModule.product', "You have a new message from {$seller->profile->firstname} {$seller->profile->lastname}"));

        return $this->htmlRedirect('/mail/mail/index');
    }
}
