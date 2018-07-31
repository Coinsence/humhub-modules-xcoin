<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 28.02.2018
 * Time: 09:54
 */

namespace humhub\modules\xcoin\controllers;


use humhub\components\Controller;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\helpers\AccountHelper;
use Yii;
use yii\db\Expression;
use yii\web\HttpException;


class AjaxController extends Controller
{
    /**
     * @param string $q
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionGetAccounts($q = '')
    {
        $owners = [];

        $query = ContentContainer::find();

        $query->limit(50);
        $query->leftJoin('user', 'user.contentcontainer_id=contentcontainer.id');
        $query->leftJoin('profile', 'user.id=profile.user_id');
        $query->leftJoin('space', 'space.contentcontainer_id=contentcontainer.id');
        $query->andFilterWhere(['LIKE', 'space.name', $q]);
        $query->orFilterWhere(['LIKE', 'user.username', $q]);
        $query->orFilterWhere(['LIKE', 'profile.firstname', $q]);
        $query->orFilterWhere(['LIKE', 'profile.lastname', $q]);

        foreach ($query->all() as $contentcontainer) {
            // Do not include container without account
            if (AccountHelper::getAccountsQuery($contentcontainer->getPolymorphicRelation())->count() == 0) {
                continue;
            }

            $owners[] = static::getOwnerInfo($contentcontainer->getPolymorphicRelation());
        }

        return $this->asJson(['results' => $owners]);
    }

    /**
     * @param $cid
     * @return \yii\web\Response
     * @throws HttpException
     */
    public function actionGetSubAccounts()
    {
        $contentContainer = ContentContainer::findOne(['id' => (int)Yii::$app->request->post('id')]);
        if ($contentContainer === null) {
            throw new HttpException('404', 'No such account!');
        }

        return $this->asJson(static::getSubAccounts($contentContainer));
    }


    /**
     * @param ContentContainerActiveRecord $containerInstance
     * @return array|null
     * @throws \Exception
     */
    public static function getOwnerInfo(ContentContainerActiveRecord $containerInstance)
    {
        $owner = null;
        if ($containerInstance instanceof User) {
            $owner = [
                'type' => 'user',
                'id' => $containerInstance->contentcontainer_id,
                'userId' => $containerInstance->id,
                'image' => UserImage::widget(['user' => $containerInstance, 'width' => 18]),
                'text' => $containerInstance->displayName,
                'displayname' => $containerInstance->displayName,
            ];
        } elseif ($containerInstance instanceof Space) {
            $owner = [
                'type' => 'space',
                'spaceId' => $containerInstance->id,
                'id' => $containerInstance->contentcontainer_id,
                'image' => SpaceImage::widget(['space' => $containerInstance, 'width' => 18]),
                'text' => $containerInstance->name,
                'title' => $containerInstance->name,
                'displayname' => $containerInstance->displayName,
            ];
        }
        return $owner;
    }


    public static function getSubAccounts(ContentContainer $contentContainer)
    {
        $subAccounts = [];

        $container = $contentContainer->getPolymorphicRelation();

        $query = AccountHelper::getAccountsQuery($container);
        if ($container instanceof User) {
            $query->andWhere(['IS', 'space_id', new Expression('NULL')]);
        }

        foreach ($query->all() as $account) {
            $accountInfo = [
                'id' => $account->id,
                'title' => $account->title,
            ];

            if ($account->user !== null) {
                $accountInfo['user'] = $account->user->getDisplayName();

                if ($account->space !== null) {
                    $accountInfo['title'] .= '- <small>' . $account->user->getDisplayName() . '</small>';
                }
            }

            $subAccounts[] = $accountInfo;
        }
        return $subAccounts;

    }

}