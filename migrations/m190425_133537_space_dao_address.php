<?php

use yii\db\Migration;

/**
 * Class m190425_133537_space_dao_address
 */
class m190425_133537_space_dao_address extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('space','dao_address',$this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('space','dao_address');
    }
}
