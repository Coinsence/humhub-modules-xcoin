<?php

use yii\db\Migration;

/**
 * Class m201113_102851_user_experience
 */
class m201113_102851_user_experience extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_experience', [
            'id' => $this->primaryKey(),
            'position' => $this->string()->notNull(),
            'employer' => $this->string()->notNull(),
            'description' => $this->text(),
            'country'=> $this->char(2),
            'city' => $this->string(),
            'start_date' => $this->date()->defaultValue(date('Y-m-d'))->notNull(),
            'end_date' => $this->date()->defaultValue(date('Y-m-d')),
            'user_id' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk_experience_user',
            'xcoin_experience',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_experience_user', 'xcoin_experience');
        $this->dropTable('xcoin_experience');
    }
}
