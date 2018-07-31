<?php

use yii\db\Migration;

class m171124_170931_accounttype extends Migration
{

    public function safeUp()
    {
        $this->addColumn('xcoin_account', 'account_type', $this->smallInteger()->defaultValue(1)->notNull());
    }

    public function safeDown()
    {
        echo "m171124_170931_accounttype cannot be reverted.\n";

        return false;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m171124_170931_accounttype cannot be reverted.\n";

      return false;
      }
     */
}
