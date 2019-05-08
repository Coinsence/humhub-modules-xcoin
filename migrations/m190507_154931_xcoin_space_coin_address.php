<?php

use yii\db\Migration;

/**
 * Class m190507_154931_xcoin_space_coin_address
 */
class m190507_154931_xcoin_space_coin_address extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('space','coin_address',$this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('space','coin_address');
    }
}
