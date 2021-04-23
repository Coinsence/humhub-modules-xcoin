<?php

use yii\db\Migration;

/**
 * Class m201123_090310_profile_offre_need
 */
class m201123_090310_profile_offre_need extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('profile_offer_need', [
            'id' => $this->primaryKey(),
            'profile_offer' =>$this->text(),
            'profile_need' => $this->text(),
            'user_id' => $this->integer()
        ]);
        $this->addForeignKey(
            'fk_profile_offer_user',
            'profile_offer_need',
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
        $this->dropForeignKey('fk_profile_offer_user', 'profile_offer_need');
        $this->dropTable('profile_offer_need');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201123_090310_profile_offre_need cannot be reverted.\n";

        return false;
    }
    */
}
