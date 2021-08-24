<?php

use yii\db\Migration;

/**
 * Class m210729_190159_update_funding_table
 */
class m210729_190159_update_funding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_funding', 'published', $this->integer()->defaultValue(1));
        $this->addColumn('xcoin_funding', 'activate_funding', $this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_funding', 'published');
        $this->dropColumn('xcoin_funding', 'activate_funding');
    }
}
