<?php

use yii\db\Migration;

/**
 * Class m201216_101153_add_column_product
 */
class m201216_101153_add_column_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_product','message', $this->text()->notNull());
        $this->addColumn('xcoin_product','type_call', $this->integer()->defaultValue(1));;
        $this->addColumn('xcoin_product','request_paytment_first',$this->integer()->defaultValue(0));
    }

    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_product','message');
        $this->dropColumn('xcoin_product','type_call');
        $this->dropColumn('xcoin_product','request_paytment_first');
    }
   
}
