<?php

use yii\db\Migration;

/**
 * Class m201202_165859_tasks_marketplace
 */
class m201202_165859_tasks_marketplace extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_marketplace','is_tasks_marketplace', $this->integer()->defaultValue(0));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_marketplace','is_tasks_marketplace');
    }
}
