<?php

use yii\db\Migration;

class uninstall extends Migration
{

    public function up()
    {
        $this->dropTable('xcoin_account');
        $this->dropTable('xcoin_asset');
        $this->dropTable('xcoin_exchange');
        $this->dropTable('xcoin_funding');
        $this->dropTable('xcoin_transaction');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
