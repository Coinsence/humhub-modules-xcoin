<?php

use yii\db\Migration;

/**
 * Class m200310_151254_funding_category_junction_table
 */
class m200310_151254_funding_category_junction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_funding_category', [
            'id' => $this->primaryKey(),
            'funding_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull()
        ]);

        // Add indexes and foreign keys
        $this->createIndex('idx-funding_category', 'xcoin_funding_category', ['funding_id', 'category_id'], true);

        $this->addForeignKey('fk_funding', 'xcoin_funding_category', 'funding_id', 'xcoin_funding', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_category', 'xcoin_funding_category', 'category_id', 'xcoin_category', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200310_151254_funding_category_junction_table cannot be reverted.\n";

        return false;
    }
}
