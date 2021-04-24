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
        $this->addColumn('xcoin_challenge', 'any_project_coin', $this->integer()->defaultValue(0));
        $this->addColumn('xcoin_challenge', 'specific_project_coin', $this->integer()->defaultValue(0));
        $this->addColumn('xcoin_challenge', 'exchange_rate', $this->integer());
        $this->addColumn('xcoin_challenge', 'selected_coin_id', $this->integer());

        // add foreign key

        $this->addForeignKey('fk_selected_coin', 'xcoin_challenge', 'selected_coin_id', 'xcoin_asset', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_challenge', 'any_project');
        $this->dropColumn('xcoin_challenge', 'no_rewarding');
        $this->dropColumn('xcoin_challenge', 'specific_project_coin');
        $this->dropColumn('xcoin_challenge', 'exchange_rate');
        $this->dropColumn('xcoin_challenge', 'selected_coin_id');
    }
}
