<?php

use yii\db\Migration;

/**
 * Class m190606_014351_space_ethereum_status
 */
class m190606_014351_space_ethereum_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('space','eth_status', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('space','eth_status');
    }
}
