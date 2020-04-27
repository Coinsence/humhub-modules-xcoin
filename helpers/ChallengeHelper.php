<?php

namespace humhub\modules\xcoin\helpers;

use humhub\modules\xcoin\models\Challenge;
use Yii;

/**
 * ChallengeHelper
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class ChallengeHelper
{
    const CAROUSEL_CHALLENGES_COUNT = 20;

    public static function getRandomChallenges()
    {
        $challengesCarousel = [];
        $randomChallenges = Challenge::find()->where(['status' => Challenge::CHALLENGE_STATUS_ENABLED])->asArray()->all();

        if ($randomChallenges) {
            if (count($randomChallenges) > self::CAROUSEL_CHALLENGES_COUNT) {
                foreach (array_rand($randomChallenges, self::CAROUSEL_CHALLENGES_COUNT) as $challenge) {

                    $challengesCarousel[] = [
                        'id' => $randomChallenges[$challenge]['id'],
                        'text' => $randomChallenges[$challenge]['title'],
                        'img' => self::getChallengeCoverUrl($randomChallenges[$challenge]['id'])
                    ];
                }
            } else {
                shuffle($randomChallenges);
                foreach ($randomChallenges as $challenge)
                    $challengesCarousel[] = [
                        'id' => $challenge['id'],
                        'text' => $challenge['title'],
                        'img' => self::getChallengeCoverUrl($challenge['id'])
                    ];
            }
        }

        return $challengesCarousel;
    }

    public static function getChallengeCoverUrl($challengeId)
    {
        $challenge = Challenge::findOne(['id' => $challengeId]);

        if ($challenge->getCover()) {
            return $challenge->getCover()->getUrl();
        } else {
            return Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-challenge-cover.png';
        }
    }
}
