<?php

use yii\db\Migration;

/**
 * Class m190412_115343_xcoin_product_optimization
 */
class m190412_115343_xcoin_product_optimization extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('xcoin_product','payment_type', 'offer_type');
        $this->alterColumn('xcoin_product', 'price', $this->float(4));
        $this->addColumn('xcoin_product','comment', $this->string(255)->notNull());
        $this->addColumn('xcoin_product','discount', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('xcoin_product','offer_type', 'payment_type');
        $this->alterColumn('xcoin_product', 'price', $this->float(4)->notNull());
        $this->dropColumn('xcoin_product','comment');
        $this->dropColumn('xcoin_product','discount');
    }
}
