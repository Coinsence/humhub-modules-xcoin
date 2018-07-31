<?php

namespace humhub\modules\xcoin\grids;

use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;
use humhub\modules\xcoin\helpers\TransactionHelper;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\grids\AccountColumn;
use humhub\libs\ActionColumn;

/**
 * Description of LatestTransactionsGridView
 *
 * @author Luke
 */
class LatestAssetTransactionsGridView extends LatestTransactionsGridView
{
    /**
     * @var \humhub\modules\content\components\ContentActiveRecord
     */
    public $contentContainer;
    
    /**
     * @var \humhub\modules\xcoin\models\Asset
     */
    public $asset;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->dataProvider = new ActiveDataProvider([
            'query' => TransactionHelper::getAssetLatest($this->asset),
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
    }

}
