<?php


namespace humhub\modules\xcoin\controllers;

use Yii;
use humhub\components\Controller;
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

        $filtered = \humhub\modules\user\widgets\UserPicker::filter([
            'keyword' => Yii::$app->request->get('keyword'),
            'fillUser' => true,
            'disableFillUser' => false
        ]);

        $result = [];

        foreach ($filtered as $user) {
            if ($space->isMember($user['id']))
                $result[] = $user;
        }


        return $result;
    }
}