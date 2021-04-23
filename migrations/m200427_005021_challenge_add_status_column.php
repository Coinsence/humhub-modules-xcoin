<?php

use yii\db\Migration;

/**
 * Class m200427_005021_challenge_add_status_column
 */
class m200427_005021_challenge_add_status_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_challenge','status', $this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_challenge','status');
    }
}
