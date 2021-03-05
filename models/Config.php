<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\models;

use Yii;
use yii\base\Model;

/**
 * Config defines the configurable fields for xcoin modlule.
 */
class Config extends Model
{

    public $isCrowdfundingEnabled;

    public function init()
    {
        parent::init();
        $module = $this->getModule();
        $this->isCrowdfundingEnabled = $module->isCrowdfundingEnabled();
    }


    public static function getModule()
    {
        return Yii::$app->getModule('xcoin');
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            [['isCrowdfundingEnabled'], 'boolean']
        ];
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return [
            'isCrowdfundingEnabled' => Yii::t('XcoinModule.base', 'Enabled Crowdfunding Feature')
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $module = static::getModule();
        $module->settings->set('isCrowdfundingEnabled', $this->isCrowdfundingEnabled);

        return true;
    }
}
