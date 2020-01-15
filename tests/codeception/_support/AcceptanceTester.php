<?php
namespace xcoin;

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
class AcceptanceTester extends \AcceptanceTester
{
    use _generated\AcceptanceTesterActions;

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

    public function createSpace($name, $description)
    {
        $this->amGoingTo('create a new xcoin space');
        $this->click('#space-menu');
        $this->waitForText('Create new space');
        $this->click('Create new space');

        $this->waitForText('Create new space', 30, '#globalModal');
        $this->fillField('Space[name]', $name);
        $this->fillField('Space[description]', $description);

        $this->click('#access-settings-link');
        $this->waitForElementVisible('.field-space-join_policy');

        // Public visibility
        $this->jsClick('#space-visibility [value="1"]');

        // Everyone can enter
        $this->jsClick('#space-join_policy [value="2"]');

        $this->click('Next', '#globalModal');

        $this->waitForText('Add Modules', 5, '#globalModal');
        $this->click('Next', '#globalModal');

        $this->waitForText('Invite members', 10, '#globalModal');
        $this->click('Done', '#globalModal');
        $this->waitForText($name);
        $this->waitForText('This space is still empty!');
    }

}
