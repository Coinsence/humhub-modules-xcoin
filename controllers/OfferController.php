<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\xcoin\models\ProfileOfferNeed;
use Yii;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Description of OfferController
 */
class OfferController extends ContentContainerController
{
    /**
     * @param null $id
     * @return string|Response
     */
    public function actionEdit($id = null)
    {
        $user = $this->contentContainer;

        if ($user->id != Yii::$app->user->id) {
            throw new HttpException(401);
        }
        if ($id) {
            $row = (new \yii\db\Query())
            ->select(['id', 'profile_offer','profile_need'])
            ->from('profile_offer_need')
            ->where(['user_id' => $id])
            ->all();
          $model=ProfileOfferNeed::find(['user_id'=>$id])->all();
        } else {
            $model = new ProfileOfferNeed();
        }
        foreach($model as $mod){
            if ($mod->load(Yii::$app->request->post()) && $mod->save()) {
                $this->view->saved();

                return  $this->htmlRedirect($user->getUrl());
            }
        }
    
        return $this->renderAjax('edit', [
            'model' => $model,
            'rows'=> $row
        ]);
 
    }

}
