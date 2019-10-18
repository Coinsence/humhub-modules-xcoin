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
    /**
     * @var int Transaction Schedule Period
     */
    public $transactionPeriod;

    /**
     * @var \humhub\modules\space\models\Space Space on which these settings are for
     */
    public $space;

    public function init()
    {
        $module = Yii::$app->getModule('xcoin');
        $this->transactionPeriod = $module->settings->space()->get('transactionPeriod');
    }

    public function showTransactionPeriod()
    {
        return $this->transactionPeriod;
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
            ['transactionPeriod', 'number', 'min' => 0, 'max' => 1]
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
        $module->settings->space()->set('transactionPeriod', $this->transactionPeriod);
        Yii::$app->queue->push(new IssueScheduledTransactions(['spaceId' => $this->space->id]));

        return true;

    }

}