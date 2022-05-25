<?php

use yii\db\Migration;

/**
 * Class m220525_151053_add_hiden_attribute_to_product
 */
class m220525_151053_add_hiden_attribute_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_product','hidden',$this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_product','hidden');
    }
}
