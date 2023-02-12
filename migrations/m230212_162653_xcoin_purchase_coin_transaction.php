<?php

use yii\db\Migration;

/**
 * Class m230212_162653_xcoin_purchase_coin_transaction
 */
class m230212_162653_xcoin_purchase_coin_transaction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_purchase_coin_transaction', [
            'id' => $this->primaryKey(),
            'asset_id' => $this->integer()->notNull(),
            'transaction_type' => $this->smallInteger()->notNull()->defaultValue(1),
            'to_account_id' => $this->integer()->notNull(),
            'from_account_id' => $this->integer()->null(),
            'amount' => $this->float(4)->notNull(),
            'created_at' => $this->dateTime()->defaultValue(date('Y-m-d H:i:s'))->notNull(),
            'key' => $this->string(50),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('xcoin_purchase_coin_transaction');
    }
}
