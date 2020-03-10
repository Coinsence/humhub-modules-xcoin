<?php


use yii\db\Migration;

/**
 * Class m200310_111228_category_table
 */
class m200310_111228_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_category', [
            'id' => $this->primaryKey(),
            'name' => $this->char(50)->notNull(),
            'slug' => $this->char(50)->notNull(),
            'created_at' => $this->dateTime()->defaultValue(date('Y-m-d H:i:s'))->notNull(),
            'created_by' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_category_creator', 'xcoin_category', 'created_by', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200310_111228_category_table cannot be reverted.\n";

        return false;
    }
}
