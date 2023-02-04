<?php

use yii\db\Migration;

/**
 * Class m230204_110603_challenge_optional_hide_fields_to_challenge_table
 */
class m230204_110603_challenge_optional_hide_fields_to_challenge_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('xcoin_challenge', 'hidden_description', $this->boolean()->defaultValue(false));
        $this->addColumn('xcoin_challenge', 'with_location_filter', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('xcoin_challenge', 'hidden_description');
        $this->dropColumn('xcoin_challenge', 'with_location_filter');

    }
}
