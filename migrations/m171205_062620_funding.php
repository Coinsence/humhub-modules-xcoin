<?php

use yii\db\Migration;

class m171205_062620_funding extends Migration
{

    public function safeUp()
    {
        $this->createTable('xcoin_funding', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'asset_id' => $this->integer()->notNull(),
            'exchange_rate' => $this->float(4)->notNull(),
            'total_amount' => $this->float(4)->notNull(),
            'available_amount' => $this->float(4)->notNull(),
            'created_at' => $this->dateTime()->defaultValue(date('Y-m-d H:i:s'))->notNull(),
            'created_by' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_funding_asset', 'xcoin_funding', 'asset_id', 'xcoin_asset', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_funding_creator', 'xcoin_funding', 'created_by', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_funding_space', 'xcoin_funding', 'space_id', 'space', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        echo "m171205_062620_funding cannot be reverted.\n";

        return false;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m171205_062620_funding cannot be reverted.\n";

      return false;
      }
     */
}
