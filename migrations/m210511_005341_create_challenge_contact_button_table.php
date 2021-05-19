<?php

use yii\db\Migration;

/**
 * Handles the creation of table `challenge_contact_button`.
 */
class m210511_005341_create_challenge_contact_button_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_challenge_contact_button', [
            'id' => $this->primaryKey(),
            'challenge_id'=>$this->integer()->notNull(),
            'status'=>$this->integer()->notNull(),
            'button_title' => $this->char(100)->notNull(),
            'popup_text' => $this->string(255),
            'receiver' => $this->char(100)->notNull(),
        ]);

        $this->addForeignKey('fk_challenge', 'xcoin_challenge_contact_button', 'challenge_id', 'xcoin_challenge', 'id', 'RESTRICT', 'RESTRICT');

    }

    public function safeDown()
    {
        echo "m210511_005341_create_challenge_contact_button_table cannot be reverted.\n";

        return false;
    }
}
