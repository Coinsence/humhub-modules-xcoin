<?php

use yii\db\Migration;

class m180105_153529_view_balance extends Migration
{

    public function up()
    {
        $sql = 'CREATE VIEW xcoin_v_account_balance AS 
                SELECT xcoin_account.id as account_id, xcoin_asset.id as asset_id, (
                    COALESCE((SELECT SUM(amount) FROM xcoin_transaction WHERE to_account_id=xcoin_account.id AND asset_id=xcoin_asset.id), 0) 
                    - 
                    COALESCE((SELECT SUM(amount) FROM xcoin_transaction WHERE from_account_id=xcoin_account.id AND asset_id=xcoin_asset.id), 0) 
                ) as balance
                FROM xcoin_account, xcoin_asset';
        $this->db->createCommand($sql)->execute();
    }

    public function safeDown()
    {
        echo "m180105_153529_view_balance cannot be reverted.\n";

        return false;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m180105_153529_view_balance cannot be reverted.\n";

      return false;
      }
     */
}
