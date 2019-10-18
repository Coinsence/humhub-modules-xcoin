<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Mortadha Ghanmi <mortadba.ghanmi56@gmail.com>
 */

namespace humhub\modules\xcoin\jobs;


use humhub\modules\queue\ActiveJob;
use Yii;

class IssueScheduledTransactions extends ActiveJob
{

    const SCHEDULE_DELAY_WEEKLY = 3600 * 24 * 7;
    const SCHEDULE_DELAY_MONTHLY = 3600 * 24 * 7 * 4;

    /**
     * @var int the space Id
     */
    public $spaceId;

    /**
     * @var int Transaction Schedule Period
     */
    private $transactionPeriod;
    private $test;

    /**
     * Issue the scheduled space transaction each set period of time
     */
    public function run()
    {

        $module = Yii::$app->getModule('xcoin');
        $this->test = $module->settings->space($this->spaceId)->get('accountTitle');
        // $transactionPeriod = $module->settings->space($this->spaceId)->get('transactionPeriod');
        $this->test .= '|e';
        $module->settings->space($this->spaceId)->set('accountTitle', $this->test);





        // Yii::$app->queue->delay()
        // TODO: Implement run() method.
    }
}