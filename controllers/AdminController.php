<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\controllers;

use humhub\modules\admin\components\Controller;
use humhub\modules\xcoin\models\Config;
use Yii;

/**
 * AdminController handles the configuration requests.
 */
class AdminController extends Controller
{

    /**
     * Configuration action for super admins.
     */
    public function actionConfig()
    {
        $form = new Config();

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            $this->view->saved();
        }

        return $this->render('config', ['model' => $form]);
    }
}
