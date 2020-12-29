<?php

use yii\db\Migration;

/**
 * Class m201216_101153_product_selling
 */
class m201216_101153_product_selling extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_product','buy_message', $this->text()->null());
        $this->addColumn('xcoin_product','payment_first',$this->integer()->defaultValue(0));
        $this->renameColumn('xcoin_marketplace','is_link_required', 'selling_option');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_product','buy_message');
        $this->dropColumn('xcoin_product','payment_first');
        $this->renameColumn('xcoin_marketplace','selling_option', 'is_link_required');
    }
}
