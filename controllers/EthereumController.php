<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\controllers;

use humhub\components\Event;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;


class EthereumController extends ContentContainerController
{
    const EVENT_ENABLE_ETHEREUM = 'enableEthereum';

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

    public function actionEnable()
    {
        $space = Space::findOne(['id' => $this->contentContainer->id]);

        Event::trigger(self::class, self::EVENT_ENABLE_ETHEREUM, new Event(['sender' => $space]));

        $space->refresh();

        return $this->asJson([
            'success' => true,
            'item' => [
                'daoAddress' => $space->dao_address,
                'coinAddress' => $space->coin_address,
            ]
        ]);
    }
}
