<?php

use yii\db\Migration;

/**
 * Class m200503_120654_challenge_add_stopped_column
 */
class m200503_120654_challenge_add_stopped_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_challenge','stopped', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_challenge','stopped');

    }
}
