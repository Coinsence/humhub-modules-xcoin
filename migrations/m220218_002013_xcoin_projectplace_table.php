<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2022 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

use yii\db\Migration;

class m220218_002013_xcoin_projectplace_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('xcoin_projectplace', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'space_id' => $this->integer()->notNull(),
            'invest_asset_id' => $this->integer()->null(),
            'reward_asset_id' => $this->integer()->null(),
            'created_at' => $this->dateTime()->defaultValue(date('Y-m-d H:i:s'))->notNull(),
            'updated_at' => $this->dateTime()->defaultValue(date('Y-m-d H:i:s'))->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_projectplace_space',
            'xcoin_projectplace',
            'space_id',
            'space',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_projectplace_invest_asset',
            'xcoin_projectplace',
            'invest_asset_id',
            'xcoin_asset',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_projectplace_reward_asset',
            'xcoin_projectplace',
            'reward_asset_id',
            'xcoin_asset',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_projectplace_created_by',
            'xcoin_projectplace',
            'created_by',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_projectplace_updated_by',
            'xcoin_projectplace',
            'updated_by',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_projectplace_space', 'xcoin_projectplace');
        $this->dropForeignKey('fk_projectplace_invest_asset', 'xcoin_projectplace');
        $this->dropForeignKey('fk_projectplace_reward_asset', 'xcoin_projectplace');
        $this->dropForeignKey('fk_projectplace_created_by', 'xcoin_projectplace');
        $this->dropForeignKey('fk_project_lace_updated_by', 'xcoin_projectplace');

        $this->dropTable('xcoin_projectplace');
    }
}
