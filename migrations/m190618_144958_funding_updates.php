<?php

use yii\db\Migration;

/**
 * Class m190618_144958_funding_updates
 */
class m190618_144958_funding_updates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('xcoin_funding', 'total_amount');
        $this->renameColumn('xcoin_funding','available_amount', 'amount');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('xcoin_funding', 'total_amount', $this->float(4)->notNull());
        $this->renameColumn('xcoin_funding','amount', 'available_amount');
    }
}
