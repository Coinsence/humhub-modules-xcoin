<?php
/**
 * Created by Mortadha Ghanmi.
 * Email: mortadha.ghanmi56@gmail.com
 */

namespace humhub\modules\xcoin\models;


use humhub\modules\xcoin\jobs\IssueScheduledTransactions;
use Yii;
use yii\base\Model;

class SpaceModuleScheduleSettings extends Model
{
    const TRANSACTION_PERIOD_NONE = -1;
    const TRANSACTION_PERIOD_WEEKLY = 0;
    const TRANSACTION_PERIOD_MONTHLY = 1;

    const SCHEDULE_DELAY_WEEKLY = 3600 * 24 * 7;
    const SCHEDULE_DELAY_MONTHLY = 3600 * 24 * 7 * 4;

    /**
     * @var int Transaction Schedule Period
     */
    public $transactionPeriod;

    /**
     * @var int The queued schedule id for later use
     */
    public $scheduleJobId;

    /**
     * @var int The date when the schedule job is pushed to the queue
     */
    public $scheduleJobPushedDate;

    /**
     * @var \humhub\modules\space\models\Space Space on which these settings are for
     */
    public $space;

    public function init()
    {
        $module = Yii::$app->getModule('xcoin');
        $this->transactionPeriod = $module->settings->space()->get('transactionPeriod');
        $this->scheduleJobId = $module->settings->space()->get('scheduleJobId');
        $this->scheduleJobPushedDate = $module->settings->space()->get('scheduleJobPushedDate');
    }

    public function showTransactionPeriod()
    {
        return $this->transactionPeriod;
    }

    public function showNextScheduledRun()
    {
        if (!$this->scheduleJobPushedDate)
            return null;

        $due = '';

        switch ($this->transactionPeriod) {
            case self::TRANSACTION_PERIOD_NONE:
                $due = 'Never';
                break;
            case self::TRANSACTION_PERIOD_WEEKLY:
                $due = date('Y/m/d H:i:s', $this->scheduleJobPushedDate + self::SCHEDULE_DELAY_WEEKLY);
                break;
            case self::TRANSACTION_PERIOD_MONTHLY:
                $due = date('Y/m/d H:i:s', $this->scheduleJobPushedDate + self::SCHEDULE_DELAY_MONTHLY);
                break;
            default:
                break;
        }

        return $due;
    }

    /**
     * Static initializer
     * @return \self
     */
    public static function instantiate()
    {
        return new self;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            ['transactionPeriod', 'number', 'min' => -1, 'max' => 1]
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'transactionPeriod' => Yii::t('XcoinModule.config', 'Transaction period'),
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $module = Yii::$app->getModule('xcoin');

        switch ($this->transactionPeriod) {
            case self::TRANSACTION_PERIOD_NONE:
                $this->removeScheduleJob();
                break;
            case self::TRANSACTION_PERIOD_WEEKLY:
                $this->runScheduleJob(self::SCHEDULE_DELAY_WEEKLY);
                break;
            case self::TRANSACTION_PERIOD_MONTHLY:
                $this->runScheduleJob(self::SCHEDULE_DELAY_MONTHLY);
                break;
            default:
                break;
        }

        return true;

    }

    private function runScheduleJob($seconds) {

        Yii::$app->queue->remove($this->scheduleJobId);

        $module = Yii::$app->getModule('xcoin');
        $module->settings->space()->set('transactionPeriod', $this->transactionPeriod);
        $scheduleJobId = Yii::$app->queue->delay($seconds)->push(new IssueScheduledTransactions(['space' => $this->space]));
        $module->settings->space()->set('scheduleJobId', $scheduleJobId);
        $module->settings->space()->set('scheduleJobPushedDate', time());

    }

    private function removeScheduleJob() {

        if ($this->scheduleJobId != null) {

            Yii::$app->queue->remove($this->scheduleJobId);

            $module = Yii::$app->getModule('xcoin');
            $module->settings->space()->set('transactionPeriod', $this->transactionPeriod);
            $module->settings->space()->set('scheduleJobId', null);
            $module->settings->space()->set('scheduleJobPushedDate', null);

        }

    }

}