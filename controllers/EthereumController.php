<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;

class EthereumController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        $space = Space::findOne(['id' => $this->contentContainer->id]);

        return $this->render('index', [
            'space' => $space,
        ]);
    }
}
