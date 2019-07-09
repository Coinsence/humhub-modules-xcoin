<?php

use yii\db\Migration;

/**
 * Class m190709_130645_increase_queue_job_db_column_size
 */
class m190709_130645_increase_queue_job_db_column_size extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('queue', 'job', 'LONGBLOB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('queue', 'job', $this->binary()->notNull());
    }
}
