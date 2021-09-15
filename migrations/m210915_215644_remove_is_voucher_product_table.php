<?php

use yii\db\Migration;

/**
 * Class m210915_215644_remove_is_voucher_product_table
 */
class m210915_215644_remove_is_voucher_product_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('xcoin_product', 'is_voucher_product');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('xcoin_product', 'is_voucher_product', $this->integer()->defaultValue(0));
    }
}
