<?php

use yii\db\Migration;

/**
 * Class m191119_154651_xcoin_funding_status
 */
class m191119_154651_xcoin_funding_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_funding', 'status', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_funding', 'status');
    }
}
