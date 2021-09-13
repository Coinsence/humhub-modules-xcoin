<?php

use yii\db\Migration;

/**
 * Class m210906_221839_product_voucher
 */
class m210906_221839_product_voucher extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_product', 'is_voucher_product', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_product', 'is_voucher_product');
    }
}
