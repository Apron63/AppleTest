<?php

namespace common\service;

use app\models\Apple;
use Cassandra\Date;
use DateTime;
use Exception;
use Yii;

/**
 * Сервисный слой.
 * Class AppleService
 * @package common\service
 */
class AppleService
{
    /**
     * Создание яблока.
     * @throws Exception
     * @return int
     */
    public static function createApple()
    {
        $apple = new Apple();
        $result = $apple->save();
        if (!$result) {
            throw new Exception('Яблоко не уродилось');
        }
        return $apple->id;
    }

    /**
     * Сбросить яблоко.
     * @param Apple $apple
     * @return int
     */
    public static function dropApple($apple)
    {
        $timeBooster = Yii::$app->session->get('timeBooster');
        if (!$timeBooster) {
            $timeBooster = 0;
        }
        $date = new DateTime();
        $date->modify("+$timeBooster hour");
        $apple->dropped_at = $date->getTimestamp();
        $apple->status = Apple::STATUS_IN_EARTH;
        return $apple->save();
    }

    /**
     * Съесть яблоко.
     * @param Apple $apple
     * @param string $percent
     * @return array
     */
    public static function eatApple($apple, $percent)
    {
        $result = [];
        $percentValue = floatval($percent);
        if ($percentValue <= 0 || $percentValue > 100) {
            $result['status'] = 'error';
            $result['message'] = 'Неправильное значение процента';
            return $result;
        }
        $newPercentValue = round($apple->size - $percentValue / 100, 2);
        if ($newPercentValue < 0) {
            $result['status'] = 'error';
            $result['message'] = 'Невозможно съесть больше остатка';
        } elseif ($newPercentValue == 0) {
            $apple->size = 0;
            $apple->status = Apple::STATUS_DESTROYED_SUCCESSFUL;
            if ($apple->save()) {
                $result['status'] = 'success';
                $result['message'] = 'Яблоко полностью съедено';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Ошибка записи яблока';
            }
        } else {
            $apple->size = $newPercentValue;
            if ($apple->save()) {
                $result['status'] = 'success';
                $result['message'] = 'Яблоко частично съедено';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Ошибка записи яблока';
            }
        }

        return $result;
    }

    /**
     * Проверка яблок со статусом Упало на то что время лежания на земле истекло.
     */
    public static function checkAppleStatuses()
    {
        $timeBooster = Yii::$app->session->get('timeBooster');
        if (!$timeBooster) {
            $timeBooster = 0;
        }
        $currentTime = new DateTime();
        $currentTime->modify("+$timeBooster hour");

        /** @var Apple $apple */
        foreach(Apple::find()->where(['status' => Apple::STATUS_IN_EARTH])->all() as $apple) {
            $date = new DateTime();
            $date->setTimestamp($apple->dropped_at);
            $date->modify("+5 hours");
            if ($currentTime >= $date) {
                $apple->status = Apple::STATUS_DESTROYED_UNSUCCESSFUL;
                $apple->save();
            }
        }
    }
}