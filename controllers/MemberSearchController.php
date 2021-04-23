<?php


namespace humhub\modules\xcoin\controllers;

use Yii;
use humhub\modules\xcoin\models\Account;
use humhub\modules\content\components\ContentContainerController;

/**
 * Search Controller provides action for searching space members.
 *
 * @author Mortadha Ghanmi
 */
class MemberSearchController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'acl' => [
                'class' => \humhub\components\behaviors\AccessControl::class,
            ]
        ];
    }

    /**
     * JSON Search for Users
     *
     * Returns an array of users with fields:
     *  - guid
     *  - displayName
     *  - image
     *  - profile link
     */
    public function actionJson()
    {
        Yii::$app->response->format = 'json';

        $space = $this->getSpace();

        $filtered_users = \humhub\modules\user\widgets\UserPicker::filter([
            'keyword' => Yii::$app->request->get('keyword'),
            'fillUser' => true,
            'disableFillUser' => false
        ]);

        $result = [];

        foreach ($filtered_users as $user) {

            $memberAccount = Account::findOne([
                'user_id' => $user['id'],
                'space_id' => $space->id,
                'account_type' => Account::TYPE_COMMUNITY_INVESTOR
            ]);

            if ($memberAccount != null) {
                $result[] = $user;
            }

        }


        return $result;
    }
}