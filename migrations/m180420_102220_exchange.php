<?php

use yii\db\Migration;

/**
 * Class m180420_102220_exchange
 */
class m180420_102220_exchange extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('xcoin_exchange');
        $this->dropColumn('xcoin_exchange', 'wanted_amount');
        $this->addColumn('xcoin_exchange','exchange_rate', $this->float(4)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180420_102220_exchange cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180420_102220_exchange cannot be reverted.\n";

        return false;
    }
    */
}
