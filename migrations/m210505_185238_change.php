<?php

use yii\db\Migration;

/**
 * Class m210505_185238_change
 */
class m210505_185238_change extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('xcoin_challenge', 'exchange_rate', $this->float());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('xcoin_challenge', 'exchange_rate', $this->integer());

    }

}
