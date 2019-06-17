<?php

use yii\db\Migration;

/**
 * Class m190617_104609_funding_account
 */
class m190617_104609_funding_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190617_104609_funding_account cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190617_104609_funding_account cannot be reverted.\n";

        return false;
    }
    */
}
