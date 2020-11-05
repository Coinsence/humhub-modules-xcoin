<?php

use yii\db\Migration;

/**
 * Class m201105_222113_tags
 */
class m201105_222113_tags extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_tag', [
            'id' => $this->primaryKey(),
            'name' => $this->char(50)->notNull(),
            'created_at' => $this->dateTime()->defaultValue(date('Y-m-d H:i:s'))->notNull(),
            'type' => $this->integer()->defaultValue(0),
            'created_by' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('xcoin_tag');
    }
}
