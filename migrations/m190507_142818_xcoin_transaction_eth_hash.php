<?php

use yii\db\Migration;

/**
 * Class m190507_142818_xcoin_transaction_eth_hash
 */
class m190507_142818_xcoin_transaction_eth_hash extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_transaction','eth_hash',$this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_transaction','eth_hash');
    }
}
