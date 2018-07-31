<?php

use yii\db\Migration;

class m171105_184625_transaction_timestamp extends Migration
{
    public function safeUp()
    {
        $this->addColumn('xcoin_transaction', 'created_at', $this->dateTime()->defaultValue(new \yii\db\Expression('NOW()'))->notNull());
    }

    public function safeDown()
    {
        echo "m171105_184625_transaction_timestamp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171105_184625_transaction_timestamp cannot be reverted.\n";

        return false;
    }
    */
}
