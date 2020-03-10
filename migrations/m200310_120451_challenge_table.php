<?php


use yii\db\Migration;

/**
 * Class m200310_120451_challenge_table
 */
class m200310_120451_challenge_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_challenge', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'asset_id' => $this->integer()->notNull(),
            'title' => $this->char(100)->notNull(),
            'country' => $this->char(2)->notNull(),
            'location' => $this->char(255)->notNull(),
            'description' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->defaultValue(date('Y-m-d H:i:s'))->notNull(),
            'created_by' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_challenge_space', 'xcoin_challenge', 'space_id', 'space', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_challenge_asset', 'xcoin_challenge', 'asset_id', 'xcoin_asset', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_challenge_creator', 'xcoin_challenge', 'created_by', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200310_120451_challenge_table cannot be reverted.\n";

        return false;
    }
}
