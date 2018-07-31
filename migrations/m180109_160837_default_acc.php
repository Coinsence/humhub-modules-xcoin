<?php

use yii\db\Migration;

class m180109_160837_default_acc extends Migration
{
    public function safeUp()
    {
        $this->update('xcoin_account', ['account_type' => 4], ['title' => 'Default']);
    }

    public function safeDown()
    {
        echo "m180109_160837_default_acc cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180109_160837_default_acc cannot be reverted.\n";

        return false;
    }
    */
}
