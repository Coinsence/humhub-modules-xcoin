<?php

use yii\db\Migration;

/**
 * Class m190617_104609_funding_account
 */
class m190617_104609_funding_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_account','funding_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropTable('funding_account');
    }
}
