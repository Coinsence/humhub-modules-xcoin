<?php

use yii\db\Migration;

/**
 * Class m201019_193802_marketplace_table
 */
class m201019_193802_marketplace_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_marketplace', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'asset_id' => $this->integer()->notNull(),
            'title' => $this->char(100)->notNull(),
            'description' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->defaultValue(date('Y-m-d H:i:s'))->notNull(),
            'created_by' => $this->integer()->notNull(),
            'status' => $this->integer()->defaultValue(1),
            'stopped' => $this->integer()->defaultValue(0),
        ]);

        $this->addForeignKey('fk_marketplace_space', 'xcoin_marketplace', 'space_id', 'space', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_marketplace_asset', 'xcoin_marketplace', 'asset_id', 'xcoin_asset', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_marketplace_creator', 'xcoin_marketplace', 'created_by', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_193802_marketplace_table cannot be reverted.\n";

        return false;
    }
}
