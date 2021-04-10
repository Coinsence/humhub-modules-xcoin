<?php
/**
 * Created by Safouane Fakhfakh.
 * Email: Safouane.Fakhfakh@mail.com
 */

namespace humhub\modules\xcoin\controllers;


use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\models\SpaceModuleAppearanceSettings;
use humhub\modules\xcoin\models\SpaceModuleBasicSettings;
use humhub\modules\xcoin\models\SpaceModuleManualSettings;
use humhub\modules\xcoin\models\SpaceModuleScheduleSettings;
use Yii;

class ConfigController extends ContentContainerController
{

    /**
     * @inheritdoc
     */
    public $validContentContainerClasses = [Space::class];

    public function actionIndex()
    {
        $model = new SpaceModuleBasicSettings();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        $space = $this->getSpace();

        return $this->render('index', [
            'model' => $model,
            'space' => $space
        ]);
    }

    public function actionSchedule()
    {
        $space = $this->getSpace();

        $model = new SpaceModuleScheduleSettings(['space' => $space]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        return $this->render('schedule', [
            'model' => $model,
            'space' => $space
        ]);
    }

    public function actionManual()
    {
        $space = $this->getSpace();

        $model = new SpaceModuleManualSettings(['space' => $space]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        return $this->render('manual', [
            'model' => $model,
            'space' => $space
        ]);
    }

    public function actionAppearance()
    {
        $space = $this->getSpace();

        $model = new SpaceModuleAppearanceSettings(['space' => $space]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        return $this->render('appearance', [
            'model' => $model,
            'space' => $space
        ]);
    }
}
