<?php

use yii\db\Migration;

/**
 * Class m220331_164639_update_marketplace_and_challenge_table
 */
class m220331_164639_update_marketplace_and_challenge_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_challenge', 'hidden', $this->integer()->defaultValue(0));
        $this->addColumn('xcoin_marketplace', 'hidden', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_challenge', 'hidden');
        $this->dropColumn('xcoin_marketplace', 'hidden');
    }
}
