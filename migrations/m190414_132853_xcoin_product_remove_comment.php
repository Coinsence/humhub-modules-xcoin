<?php

use yii\db\Migration;

/**
 * Class m190414_132853_xcoin_product_comment
 */
class m190414_132853_xcoin_product_remove_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('xcoin_product', 'comment');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('xcoin_product','comment', $this->string(255)->notNull());

        return false;
    }
}
