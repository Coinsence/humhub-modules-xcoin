<?php

namespace humhub\modules\xcoin\widgets;

use humhub\modules\xcoin\models\Asset;
use humhub\components\Widget;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\AccountBalance;

/**
 * Description of AmountField
 *
 * @author Luke
 */
class AssetDistribution extends Widget
{

    /**
     * @var Asset
     */
    public $asset;

    public function run()
    {
        return $this->render('asset-distribution', ['distributions' => self::getDistributionArray($this->asset)]);
    }

    public static function getDistributionArray(Asset $asset)
    {
        $result = [];

        
        $issuedAmount = $asset->getIssuedAmount();
        
        foreach (AccountBalance::find()->where(['asset_id' => $asset->id])->andWhere('balance > 0')->all() as $balance) {
            $account = $balance->account;

            $contentContainer = ($account->space !== null) ? $account->space : $account->user;
            $id = $contentContainer->contentContainerRecord->id;


            if (!isset($result[$id])) {
                $result[$id]['record'] = $contentContainer;
                $result[$id]['balance'] = 0;
            }

            $result[$id]['balance'] += $balance->balance;
            $result[$id]['percent'] = round(($result[$id]['balance'] / $issuedAmount)*100,4);            
        }

        usort($result, function ($a, $b) {
            return $b['balance'] - $a['balance'];
        });


        return $result;
    }

}
