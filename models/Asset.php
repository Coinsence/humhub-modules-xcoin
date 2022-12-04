<?php

namespace humhub\modules\xcoin\models;

use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\space\models\Space;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "xcoin_asset".
 *
 * @property integer $id
 * @property integer $space_id
 * @property string $title
 * @property string $algorand_asset_id
 *
 * @property Space $space
 * @property Transaction[] $xcoinTransactions
 */
class Asset extends ActiveRecord
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
            [['space_id', 'title'], 'unique', 'targetAttribute' => ['space_id', 'title'], 'message' => Yii::t('XcoinModule.base', 'The combination of Space ID and Title has already been taken.')],
            [['space_id'], 'exist', 'skipOnError' => true, 'targetClass' => Space::className(), 'targetAttribute' => ['space_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('XcoinModule.base', 'ID'),
            'space_id' => Yii::t('XcoinModule.base', 'Space ID'),
            'title' => Yii::t('XcoinModule.base', 'Title'),
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
     * @return ActiveQuery
     */
    public function getSpace()
    {
        return $this->hasOne(Space::className(), ['id' => 'space_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::className(), ['asset_id' => 'id']);
    }

    public function getIssuedAmount()
    {
        $issueAccount = AccountHelper::getIssueAccount($this->space);
        $query = AccountBalance::find()->where(['asset_id' => $this->id])->andWhere(['!=', 'account_id', $issueAccount->id]);

        return $query->sum('balance');
    }

}
