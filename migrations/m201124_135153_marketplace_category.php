<?php

use yii\db\Migration;

/**
 * Class m201124_135153_marketplace_category
 */
class m201124_135153_marketplace_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_marketplace_category', [
            'id' => $this->primaryKey(),
            'marketplace_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull()
        ]);

        // Add indexes and foreign keys
        $this->createIndex('idx-marketplace_category', 'xcoin_marketplace_category', ['marketplace_id', 'category_id'], true);

        $this->addForeignKey('fk_mark', 'xcoin_marketplace_category', 'marketplace_id', 'xcoin_marketplace', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_cat', 'xcoin_marketplace_category', 'category_id', 'xcoin_category', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('marketplace_category', 'xcoin_marketplace_category');

        $this->dropForeignKey('fk_mark', 'xcoin_marketplace_category');
        $this->dropForeignKey('fk_cat', 'xcoin_marketplace_category');

        $this->dropTable('xcoin_marketplace_category');
    }
}
