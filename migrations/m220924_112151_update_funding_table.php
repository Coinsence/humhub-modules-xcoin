<?php

use yii\db\Migration;

/**
 * Class m220924_112151_update_funding_table
 */
class m220924_112151_update_funding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_funding', 'hidden_location', $this->integer()->defaultValue(0));
        $this->addColumn('xcoin_funding', 'hidden_details', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_funding', 'hidden_location');
        $this->dropColumn('xcoin_funding', 'hidden_details');
    }
}
