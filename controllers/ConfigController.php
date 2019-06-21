<?php
/**
 * Created by Safouane Fakhfakh.
 * Email: Safouane.Fakhfakh@mail.com
 */

namespace humhub\modules\xcoin\controllers;


use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\models\SpaceModuleSettings;
use Yii;

class ConfigController extends ContentContainerController
{

    /**
     * @inheritdoc
     */
    public $validContentContainerClasses = [Space::class];

    public function actionIndex()
    {
        $model = new SpaceModuleSettings();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }
        return $this->render('snippet', [
            'model' => $model
        ]);
    }
}