<?php

use yii\db\Migration;

/**
 * Class m190717_135531_review_funding_product
 */
class m190717_135531_review_funding_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_product', 'review_status', $this->integer()->defaultValue(0));
        $this->addColumn('xcoin_funding', 'review_status', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_product', 'review_status');
        $this->dropColumn('xcoin_funding', 'review_status');
    }
}
