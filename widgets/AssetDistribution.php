<?php

namespace humhub\modules\xcoin\widgets;

use GuzzleHttp\Exception\GuzzleException;
use humhub\modules\algorand\calls\Coin;
use humhub\modules\algorand\models\AssetHolder;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Asset;
use humhub\components\Widget;
use yii\web\BadRequestHttpException;

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

    /**
     * @throws GuzzleException
     * @throws BadRequestHttpException
     */
    public static function getDistributionArray(Asset $asset)
    {
        $result = [];

        $issuedAmount = $asset->getIssuedAmount();

        /** @var AssetHolder $assetHolder */
        foreach (Coin::assetHolders($asset) as $assetHolder) {
            $account = $assetHolder->getAccount();

            if (!$account instanceof Account) {
                continue;
            }

            $contentContainer = ($account->space !== null) ? $account->space : $account->user;
            $id = $contentContainer->contentContainerRecord->id;


            if (!isset($result[$id])) {
                $result[$id]['record'] = $contentContainer;
                $result[$id]['balance'] = 0;
            }

            $result[$id]['balance'] += $assetHolder->balance;
            $result[$id]['percent'] = round(($result[$id]['balance'] / $issuedAmount)*100,4);            
        }

        usort($result, function ($a, $b) {
            return $b['balance'] - $a['balance'];
        });


        return $result;
    }

}
