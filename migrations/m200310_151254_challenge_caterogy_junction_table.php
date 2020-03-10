<?php

use yii\db\Migration;

/**
 * Class m200310_151254_challenge_caterogy_junction_table
 */
class m200310_151254_challenge_caterogy_junction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_categories_challenges', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'challenge_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_category', 'xcoin_categories_challenges', 'category_id', 'xcoin_category', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_challenge', 'xcoin_categories_challenges', 'challenge_id', 'xcoin_challenge', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200310_151254_challenge_caterogy_junction_table cannot be reverted.\n";

        return false;
    }
}
