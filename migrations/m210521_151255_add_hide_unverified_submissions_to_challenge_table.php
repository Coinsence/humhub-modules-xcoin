<?php

use yii\db\Migration;

/**
 * Class m210521_151255_add_hide_unverified_submissions_to_challenge_table
 */
class m210521_151255_add_hide_unverified_submissions_to_challenge_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_marketplace', 'hide_unverified_submissions', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_marketplace', 'hide_unverified_submissions');
    }
}
