<?php

use yii\db\Migration;

/**
 * Class m220615_223459_alogran_asset_id
 */
class m220615_223459_algorand_asset_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_asset','algorand_asset_id', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_asset','algorand_asset_id');
    }

}
