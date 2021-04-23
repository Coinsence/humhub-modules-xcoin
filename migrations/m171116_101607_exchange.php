<?php

use yii\db\Migration;

class m171116_101607_exchange extends Migration
{

    public function safeUp()
    {
        $this->createTable('xcoin_exchange', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer()->notNull(),
            'asset_id' => $this->integer()->notNull(),
            'total_amount' => $this->float(4)->notNull(),
            'available_amount' => $this->float(4)->notNull(),
            'minimum_amount' => $this->float(4)->notNull(),
            'wanted_asset_id' => $this->integer()->notNull(),
            'wanted_amount' => $this->float(4)->notNull(),
            'created_at' => $this->dateTime()->defaultValue(date('Y-m-d H:i:s'))->notNull(),
        ]);

        $this->addForeignKey('fk_offer_account', 'xcoin_exchange', 'account_id', 'xcoin_account', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_offer_asset', 'xcoin_exchange', 'asset_id', 'xcoin_asset', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_wanted_asset', 'xcoin_exchange', 'wanted_asset_id', 'xcoin_asset', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        echo "m171116_101607_exchange cannot be reverted.\n";

        return false;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m171116_101607_exchange cannot be reverted.\n";

      return false;
      }
     */
}
