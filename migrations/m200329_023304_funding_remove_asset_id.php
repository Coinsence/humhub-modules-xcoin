<?php

use yii\db\Migration;

/**
 * Class m200329_023304_funding_remove_asset_id
 */
class m200329_023304_funding_remove_asset_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_funding_asset','xcoin_funding');
        $this->dropColumn('xcoin_funding','asset_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200329_023304_funding_remove_asset_id cannot be reverted.\n";

        return false;
    }
}
