<?php

use yii\db\Migration;

/**
 * Class m190709_151801_investor_account
 */
class m190709_151801_investor_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_account','investor_id',$this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_account','investor_id');
    }
}
