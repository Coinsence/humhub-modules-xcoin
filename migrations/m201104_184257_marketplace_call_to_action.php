<?php

use yii\db\Migration;

/**
 * Class m201104_184257_product_call_to_action_button
 */
class m201104_184257_marketplace_call_to_action extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_marketplace','action_name', $this->char(255)->null());
        $this->addColumn('xcoin_marketplace','is_link_required', $this->integer()->defaultValue(0));
        $this->addColumn('xcoin_product','link', $this->text()->null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_marketplace', 'action_name');
        $this->dropColumn('xcoin_marketplace', 'is_link_required');
        $this->dropColumn('xcoin_product', 'link');
    }
}
