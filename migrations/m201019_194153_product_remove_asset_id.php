<?php

use yii\db\Migration;

/**
 * Class m201019_194153_product_remove_asset_id
 */
class m201019_194153_product_remove_asset_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_product_asset','xcoin_product');
        $this->dropColumn('xcoin_product','asset_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_194153_product_remove_asset_id cannot be reverted.\n";

        return false;
    }
}
