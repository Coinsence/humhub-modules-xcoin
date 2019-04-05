<?php

use yii\db\Migration;

/**
 * Class m190404_120903_xcoin_product
 */
class m190404_120903_xcoin_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_product', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->string(255)->notNull(),
            'asset_id' => $this->integer()->notNull(),
            'price' => $this->float(4)->notNull(),
            'content' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->defaultValue(date('Y-m-d H:i:s'))->notNull(),
            'created_by' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_product_asset', 'xcoin_product', 'asset_id', 'xcoin_asset', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_product_owner', 'xcoin_product', 'created_by', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_product_asset', 'xcoin_product');
        $this->dropForeignKey('fk_product_owner', 'xcoin_product');

        $this->dropTable('xcoin_product');
    }
}
