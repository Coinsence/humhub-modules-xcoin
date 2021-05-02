<?php

use yii\db\Migration;

/**
 * Class m210429_024001_add_sender_account_funding
 */
class m210429_024001_add_sender_account_funding extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_funding', 'specific_sender_account_id', $this->integer()->defaultValue(null));

        // add foreign key

        $this->addForeignKey('fk_specific_sender_account_sender', 'xcoin_funding', 'specific_sender_account_id', 'xcoin_account', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_funding', 'specific_sender_account_id');
        $this->dropForeignKey('fk_specific_sender_account_sender', 'xcoin_funding');
    }
}
