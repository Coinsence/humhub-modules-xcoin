<?php

use yii\db\Migration;

/**
 * Class m211207_222340_account_alogrand_update
 */
class m211207_222340_account_alogrand_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_account','algorand_public_key',$this->string(100));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_account','algorand_public_key');
    }
}
