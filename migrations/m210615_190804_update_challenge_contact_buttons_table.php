<?php

use yii\db\Migration;

/**
 * Class m210615_190804_update_challenge_contact_buttons_table
 */
class m210615_190804_update_challenge_contact_buttons_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->alterColumn('xcoin_challenge_contact_button','button_title',$this->char(100)->null());
    $this->alterColumn('xcoin_challenge_contact_button','popup_text',$this->string(255)->null());
    $this->alterColumn('xcoin_challenge_contact_button','receiver',$this->char(100)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210615_190804_update_challenge_contact_buttons_table cannot be reverted.\n";

        return false;
    }
}
