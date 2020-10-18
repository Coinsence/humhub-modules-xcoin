<?php

use yii\db\Migration;

/**
 * Class m201018_212236_product_category_junction_table
 */
class m201018_212236_product_category_junction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_product_category', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull()
        ]);

        // Add indexes and foreign keys
        $this->createIndex('idx-product_category', 'xcoin_product_category', ['product_id', 'category_id'], true);

        $this->addForeignKey('fk_product', 'xcoin_product_category', 'product_id', 'xcoin_product', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_category', 'xcoin_product_category', 'category_id', 'xcoin_category', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201018_212236_product_category_junction_table cannot be reverted.\n";

        return false;
    }
}
