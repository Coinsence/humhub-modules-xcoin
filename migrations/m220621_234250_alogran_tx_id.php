<?php

use yii\db\Migration;

/**
 * Class m220621_234250_alogran_tx_id
 */
class m220621_234250_alogran_tx_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('xcoin_transaction', 'eth_hash');
        $this->addColumn('xcoin_transaction','algorand_tx_id',$this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_transaction', 'algorand_tx_id');
        $this->addColumn('xcoin_account','eth_hash',$this->string(50));
    }
}
