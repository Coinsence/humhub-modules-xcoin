<?php

use yii\db\Migration;

/**
 * Class m200310_123101_update_funding_table
 */
class m200310_123101_update_funding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_funding','challenge_id', $this->integer());
        $this->addColumn('xcoin_funding','country', $this->char(2));
        $this->addColumn('xcoin_funding','city', $this->char(255));

        $this->addForeignKey('fk_funding_challenge', 'xcoin_funding', 'challenge_id', 'xcoin_challenge', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200310_123101_update_funding_table cannot be reverted.\n";

        return false;
    }
}
