<?php

use yii\db\Migration;

/**
 * Class m210504_215525_remove_sender_account_xcoin_funding_table
 */
class m210504_215525_remove_sender_account_xcoin_funding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_specific_sender_account_sender', 'xcoin_funding');
        $this->dropColumn('xcoin_funding', 'specific_sender_account_id');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210504_215525_remove_sender_account_xcoin_funding_table cannot be reverted.\n";

        return false;
    }
    */
}
