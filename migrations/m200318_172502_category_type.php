<?php

use yii\db\Migration;

/**
 * Class m200318_172502_category_type
 */
class m200318_172502_category_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_category','type', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200318_172502_category_type cannot be reverted.\n";

        return false;
    }
}
