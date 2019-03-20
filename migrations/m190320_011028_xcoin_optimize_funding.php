<?php

use yii\db\Migration;

/**
 * Class m190320_011028_xcoin_optimize_funding
 */
class m190320_011028_xcoin_optimize_funding extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_funding','title', $this->string(255)->notNull());
        $this->addColumn('xcoin_funding','description', $this->text()->notNull());
        $this->addColumn('xcoin_funding', 'deadline', $this->dateTime()->notNull());
        $this->addColumn('xcoin_funding','needs', $this->text()->notNull());
        $this->addColumn('xcoin_funding','commitments', $this->text()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_funding','title');
        $this->dropColumn('xcoin_funding','description');
        $this->dropColumn('xcoin_funding', 'deadline');
        $this->dropColumn('xcoin_funding','needs');
        $this->dropColumn('xcoin_funding','commitments');
    }
}
