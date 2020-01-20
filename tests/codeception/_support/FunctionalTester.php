<?php
namespace xcoin;

use humhub\modules\space\models\Space;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \FunctionalTester
{
    use _generated\FunctionalTesterActions;
    
   /**
    * Define custom actions here
    */

    public function amOnCrowdfunding($params = [])
    {
        return tests\codeception\_pages\CrowdfundingPage::openBy($this, $params);
    }

    public function amOnMarketplace($params = [])
    {
        return tests\codeception\_pages\MarketplacePage::openBy($this, $params);
    }

    public function createSpace($name, $description, $color)
    {
        $this->sendAjaxPostRequest('index.php?r=space/create/create', [
            'Space[color]' => $color,
            'Space[name]' => $name,
            'Space[description]' => $description,
            'Space[visibility]' => 1,
            'Space[join_policy]' => 2
        ]);
        $this->seeRecord(Space::class, [
            'name' => $name,
            'visibility' => 1,
            'join_policy' => 2,
        ]);
    }

}
