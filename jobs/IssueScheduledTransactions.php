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

    const TRANSACTION_PERIOD_NONE = -1;
    const TRANSACTION_PERIOD_WEEKLY = 0;
    const TRANSACTION_PERIOD_MONTHLY = 1;

    const SCHEDULE_DELAY_WEEKLY = 3600 * 24 * 7;
    const SCHEDULE_DELAY_MONTHLY = 3600 * 24 * 7 * 4;

    /**
     * @var \humhub\modules\space\models\Space Space on which these settings are for
     */
    public $space;

    /**
     * This will make the job re-executed again in the future like a cron
     */
    private function cron($seconds) {

        $module = Yii::$app->getModule('xcoin');
        $scheduleJobId = Yii::$app->queue->delay($seconds)->push(new IssueScheduledTransactions(['space' => $this->space]));
        $module->settings->contentContainer($this->space)->set('scheduleJobId', $scheduleJobId);

    }

    /**
     * Issue the scheduled space transaction each set period of time
     */
    public function run()
    {

        $module = Yii::$app->getModule('xcoin');

        // TODO: do the coin allocation logic here

        $transactionPeriod = $module->settings->contentContainer($this->space)->get('transactionPeriod');
        switch ($transactionPeriod) {
            case self::TRANSACTION_PERIOD_NONE:
                break;
            case self::TRANSACTION_PERIOD_WEEKLY:
                $this->cron(self::SCHEDULE_DELAY_WEEKLY);
                break;
            case self::TRANSACTION_PERIOD_MONTHLY:
                $this->cron(self::SCHEDULE_DELAY_MONTHLY);
                break;
            default:
                break;
        }
    }
}