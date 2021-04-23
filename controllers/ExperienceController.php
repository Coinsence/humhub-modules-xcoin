<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\xcoin\models\Experience;
use Yii;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Description of ExperienceController
 */
class ExperienceController extends ContentContainerController
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
            $model = Experience::findOne(['id' => $id, 'user_id' => $user->id]);
        } else {
            $model = new Experience();
            $model->actual_position = true;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();

            return $this->htmlRedirect($user->getUrl());
        }

        return $this->renderAjax('edit', [
            'model' => $model,
        ]);
    }

    /**
     * @param null $id
     * @return string|Response
     */
    public function actionDelete($id = null)
    {
        $user = $this->contentContainer;

        if ($user->id != Yii::$app->user->id) {
            throw new HttpException(401);
        }

        $model = Experience::findOne(['id' => $id, 'user_id' => $user->id]);

        if (!$model) {
            throw new HttpException(404, 'Experience not found');
        }

        $model->delete();
        $this->view->saved();

        return $this->htmlRedirect($user->getUrl());
    }
}
