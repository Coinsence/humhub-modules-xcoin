<?php

namespace humhub\modules\xcoin\models;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

/**
 * This is the model class for table "xcoin_asset".
 *
 * @property integer $id
 * @property integer $space_id
 * @property string $title
 *
 * @property Space $space
 * @property TcoinTransaction[] $xcoinTransactions
 */
class Asset extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_asset';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['space_id', 'title'], 'required'],
            [['space_id'], 'integer'],
            [['title'], 'string', 'max' => 20],
            [['space_id', 'title'], 'unique', 'targetAttribute' => ['space_id', 'title'], 'message' => 'The combination of Space ID and Title has already been taken.'],
            [['space_id'], 'exist', 'skipOnError' => true, 'targetClass' => Space::className(), 'targetAttribute' => ['space_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'space_id' => 'Space ID',
            'title' => 'Title',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'title' => 'A short description of this asset',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpace()
    {
        return $this->hasOne(Space::className(), ['id' => 'space_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::className(), ['asset_id' => 'id']);
    }

    public function getIssuedAmount()
    {
        #$plus = Transaction::find()->andWhere(['asset_id' => $asset->id])->sum('amount');
        #$minus = Transaction::find()->where(['from_account_id' => $this->id])->andWhere(['asset_id' => $asset->id])->sum('amount');
        #return $plus - $minus;
        #return Transaction::find()->andWhere(['asset_id' => $this->id, 'transaction_type' => Transaction::TRANSACTION_TYPE_ISSUE])->sum('amount');

        $issueAccount = \humhub\modules\xcoin\helpers\AccountHelper::getIssueAccount($this->space);
        $query = AccountBalance::find()->where(['asset_id' => $this->id])->andWhere(['!=', 'account_id', $issueAccount->id]);

        return $query->sum('balance');
    }

}
