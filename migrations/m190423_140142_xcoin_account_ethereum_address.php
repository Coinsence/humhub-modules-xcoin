<?php

use yii\db\Migration;

/**
 * Class m190423_140142_xcoin_account_ethereum_address
 */
class m190423_140142_xcoin_account_ethereum_address extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_account','ethereum_address',$this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_account','ethereum_address');
    }
}
