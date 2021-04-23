<?php

use yii\db\Migration;

/**
 * Class m190507_173512_xcoin_xcoin_transaction_hash_update
 */
class m190507_173512_xcoin_xcoin_transaction_hash_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('xcoin_transaction', 'eth_hash', $this->string(100));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('xcoin_transaction', 'eth_hash', $this->string(50));
    }
}
