<?php

use yii\db\Migration;

/**
 * Class m220920_100722_account_voucher_updates
 */
class m220920_100722_account_voucher_updates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_account_voucher','redeemed_account_id', $this->integer());
        $this->addColumn('xcoin_account_voucher','tag', $this->char(255));

        $this->addForeignKey('fk_voucher_redeemed_account', 'xcoin_account_voucher', 'redeemed_account_id', 'xcoin_account', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200310_123101_update_funding_table cannot be reverted.\n";

        return false;
    }
}
