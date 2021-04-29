<?php

use yii\db\Migration;

/**
 * Class m210423_015635_add_investor_reward_challenge
 */
class m210423_015635_add_investor_reward_challenge extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_challenge', 'no_rewarding', $this->integer()->defaultValue(0));
        $this->addColumn('xcoin_challenge', 'any_reward_asset', $this->integer()->defaultValue(0));
        $this->addColumn('xcoin_challenge', 'specific_reward_asset', $this->integer()->defaultValue(0));
        $this->addColumn('xcoin_challenge', 'exchange_rate', $this->integer());
        $this->addColumn('xcoin_challenge', 'specific_reward_asset_id', $this->integer());

        // add foreign key

        $this->addForeignKey('fk_reward_asset', 'xcoin_challenge', 'specific_reward_asset_id', 'xcoin_asset', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_challenge', 'any_reward_asset');
        $this->dropColumn('xcoin_challenge', 'no_rewarding');
        $this->dropColumn('xcoin_challenge', 'exchange_rate');
        $this->dropColumn('xcoin_challenge', 'specific_reward_asset');
        $this->dropColumn('xcoin_challenge', 'specific_reward_asset_id');
    }
}
