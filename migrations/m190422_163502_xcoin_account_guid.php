<?php

use yii\db\Migration;

/**
 * Class m190422_163502_xcoin_account_guid
 */
class m190422_163502_xcoin_account_guid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_account', 'guid', $this->string(45)->notNull());
        $this->createIndex('unique_guid', 'xcoin_account', 'guid', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('unique_guid', 'xcoin_account');
        $this->dropColumn('xcoin_account', 'guid');
    }
}
