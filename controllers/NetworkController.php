<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\controllers;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\components\Controller;
use humhub\modules\xcoin\models\Tag;

class NetworkController extends Controller
{
    const TYPE_USER = 'user';
    const TYPE_SPACE = 'space';

    public function actionIndex($type = self::TYPE_USER, $tag = null)
    {
        $query = $type == self::TYPE_SPACE ? Space::find() : User::find();

        if ($tag) {
            $query->andWhere(['like', 'tags', '%' . $tag. '%', false]);
        }

        return $this->render('index', [
            'results' => $query->all(),
            'tags' => Tag::find()->all(),
            'type' => $type
        ]);
    }
}
