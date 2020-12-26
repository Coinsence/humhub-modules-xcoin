<?php

use yii\db\Migration;

/**
 * Class m201124_154649_archive_account
 */
class m201124_154649_archive_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_account','archived', $this->integer()->defaultValue(0));
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_account', 'archived');
    }
}
