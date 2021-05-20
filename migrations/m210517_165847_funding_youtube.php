<?php

use yii\db\Migration;

/**
 * Class m210517_165847_funding_youtube
 */
class m210517_165847_funding_youtube extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_funding', 'youtube_link', $this->char(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_funding', 'youtube_link');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210517_165847_funding_youtube cannot be reverted.\n";

        return false;
    }
    */
}
