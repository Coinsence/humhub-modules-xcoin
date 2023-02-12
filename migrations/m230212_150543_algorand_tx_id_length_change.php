<?php

use yii\db\Migration;

/**
 * Class m230212_150543_algorand_tx_id_length_change
 */
class m230212_150543_algorand_tx_id_length_change extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('xcoin_transaction', 'algorand_tx_id', $this->string(100));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('xcoin_transaction', 'algorand_tx_id', $this->string(50));
    }
}
