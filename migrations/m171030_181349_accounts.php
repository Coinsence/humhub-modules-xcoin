<?php

use yii\db\Migration;

class m171030_181349_accounts extends Migration
{

    public function safeUp()
    {
        $this->createTable('xcoin_account', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'space_id' => $this->integer()->null(),
            'title' => $this->char(100)->notNull(),
        ]);
        $this->addForeignKey('fk_account_space', 'xcoin_account', 'space_id', 'space', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_account_user', 'xcoin_account', 'user_id', 'user', 'id', 'RESTRICT', 'RESTRICT');
        $this->createIndex('i_account_title', 'xcoin_account', ['user_id', 'space_id', 'title'], true);

        $this->createTable('xcoin_asset', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'title' => $this->char(20)->notNull(),
        ]);
        $this->addForeignKey('fk_asset_space', 'xcoin_asset', 'space_id', 'space', 'id', 'RESTRICT', 'RESTRICT');
        $this->createIndex('i_asset_title', 'xcoin_asset', ['space_id', 'title'], true);

        $this->createTable('xcoin_transaction', [
            'id' => $this->primaryKey(),
            'asset_id' => $this->integer()->notNull(),
            'transaction_type' => $this->smallInteger()->notNull()->defaultValue(1),
            'to_account_id' => $this->integer()->notNull(),
            'from_account_id' => $this->integer()->null(),
            'amount' => $this->float(4)->notNull(),
            'comment' => $this->char(200)->null(),
        ]);
        $this->addForeignKey('fk_transaction_asset', 'xcoin_transaction', 'asset_id', 'xcoin_asset', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_transaction_from', 'xcoin_transaction', 'from_account_id', 'xcoin_account', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_transaction_to', 'xcoin_transaction', 'to_account_id', 'xcoin_account', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function safeDown()
    {
        echo "m171030_181349_accounts cannot be reverted.\n";

        return false;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m171030_181349_accounts cannot be reverted.\n";

      return false;
      }
     */
}
