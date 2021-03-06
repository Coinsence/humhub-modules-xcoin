<?php
/**
 * Created by Mortadha Ghanmi.
 * Email: mortadha.ghanmi@beecoop.co
 *
 * @var $this yii\web\View
 * @var $model \humhub\modules\xcoin\models\SpaceModuleSettings
 */


namespace humhub\modules\xcoin\widgets;

use Yii;

/**
 * Module Configuration Menu
 */
class ConfigurationMenu extends \humhub\widgets\BaseMenu
{
    /**
     * @inheritdoc
     */
    public $template = "@humhub/widgets/views/tabMenu";

    /**
     * @var \humhub\modules\space\models\Space
     */
    public $space;

    public function init()
    {
        $this->addItem([
            'label' => Yii::t('XcoinModule.config', 'Basic'),
            'url' => $this->space->createUrl('/xcoin/config/index'),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'index'),
        ]);

        $this->addItem([
            'label' => Yii::t('XcoinModule.config', 'Scheduling'),
            'url' => $this->space->createUrl('/xcoin/config/schedule'),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'schedule'),
        ]);

        $this->addItem([
            'label' => Yii::t('XcoinModule.config', 'Manual'),
            'url' => $this->space->createUrl('/xcoin/config/manual'),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'manual'),
        ]);

        $this->addItem([
            'label' => Yii::t('XcoinModule.config', 'Appearance'),
            'url' => $this->space->createUrl('/xcoin/config/appearance'),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'appearance'),
        ]);



        parent::init();
    }

}