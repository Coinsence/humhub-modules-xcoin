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

        if ($space->eth_status == Space::ETHEREUM_STATUS_DISABLED) {
            $space->updateAttributes(['eth_status' => Space::ETHEREUM_STATUS_IN_PROGRESS]);
            
            Event::trigger(self::class, self::EVENT_ENABLE_ETHEREUM, new Event(['sender' => $space]));
        }

        return $this->asJson([
            'success' => true,
        ]);
    }
}
