<?php

use yii\db\Migration;

/**
 * Class m201019_194425_update_product_table
 */
class m201019_194425_update_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_product','marketplace_id', $this->integer());
        $this->addColumn('xcoin_product','country', $this->char(2));
        $this->addColumn('xcoin_product','city', $this->char(255));

        $this->addForeignKey('fk_product_marketplace', 'xcoin_product', 'marketplace_id', 'xcoin_marketplace', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_194425_update_product_table cannot be reverted.\n";

        return false;
    }
}
