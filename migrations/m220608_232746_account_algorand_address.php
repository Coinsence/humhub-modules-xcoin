<?php

use yii\db\Migration;

/**
 * Class m220608_232746_account_algorand_address
 */
class m220608_232746_account_algorand_address extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('xcoin_account', 'ethereum_address');
        $this->addColumn('xcoin_account','algorand_address',$this->string(100));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_account', 'algorand_address');
        $this->addColumn('xcoin_account','ethereum_address',$this->string(50));
    }
}
