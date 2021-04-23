<?php

use yii\db\Migration;

class m171204_171513_exchange_creator extends Migration
{

    public function safeUp()
    {
        $this->addColumn('xcoin_exchange', 'created_by', $this->integer()->notNull());
        $this->addForeignKey('fk_exchange_creator', 'xcoin_exchange', 'created_by', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        echo "m171204_171513_exchange_creator cannot be reverted.\n";

        return false;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m171204_171513_exchange_creator cannot be reverted.\n";

      return false;
      }
     */
}
