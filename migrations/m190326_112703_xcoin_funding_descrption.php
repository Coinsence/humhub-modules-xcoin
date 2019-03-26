<?php

use yii\db\Migration;

/**
 * Class m190326_112703_xcoin_funding_descrption
 */
class m190326_112703_xcoin_funding_descrption extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('xcoin_funding', 'description', $this->string(255)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('xcoin_funding','description', $this->text()->notNull());
    }
}
