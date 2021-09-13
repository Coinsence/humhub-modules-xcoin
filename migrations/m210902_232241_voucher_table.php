<?php

use yii\db\Migration;

/**
 * Class m210902_232241_voucher_table
 */
class m210902_232241_voucher_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('xcoin_voucher', [
            'id' => $this->primaryKey(),
            'value' => $this->text()->notNull(),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'product_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_voucher_product', 'xcoin_voucher', 'product_id', 'xcoin_product', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_voucher_product', 'xcoin_voucher');
        $this->dropTable('xcoin_voucher');
    }
}
