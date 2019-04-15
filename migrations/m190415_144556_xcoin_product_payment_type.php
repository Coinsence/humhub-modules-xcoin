<?php

use yii\db\Migration;

/**
 * Class m190415_144556_xcoin_product_payment_type
 */
class m190415_144556_xcoin_product_payment_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_product','payment_type', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_product','payment_type');
    }
}
