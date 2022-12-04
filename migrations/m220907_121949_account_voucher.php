<?php

use yii\db\Migration;

/**
 * Class m220907_121949_account_voucher
 */
class m220907_121949_account_voucher extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_account_voucher', [
            'id' => $this->primaryKey(),
            'value' => $this->text()->notNull(),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'amount'=>$this->float()->notNull(),
            'account_id' => $this->integer()->notNull(),
            'asset_id'=>$this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_voucher_account', 'xcoin_account_voucher', 'account_id', 'xcoin_account', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_voucher_asset', 'xcoin_account_voucher', 'asset_id', 'xcoin_asset', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_voucher_account', 'xcoin_account_voucher');
        $this->dropForeignKey('fk_voucher_asset', 'xcoin_account_voucher');
        $this->dropTable('xcoin_account_voucher');
    }
}
