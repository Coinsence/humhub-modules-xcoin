<?php

use yii\db\Migration;

/**
 * Class m190620_184605_space_type
 */
class m190620_184605_space_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('space', 'space_type', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('space', 'space_type');
    }
}
