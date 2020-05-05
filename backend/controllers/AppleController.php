<?php

namespace backend\controllers;

use app\models\Apple;
use common\service\AppleService;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class AppleController
 * @package backend\controllers
 */
class AppleController extends Controller
{
    /**
     * Создаем яблоко.
     * @return Response
     * @throws Exception
     */
    public function actionCreate()
    {
        AppleService::createApple();
        return $this->redirect(['site/index']);
    }


    /**
     * Сбрасываем с дерева.
     * @param $id
     * @return Response
     */
    public function actionDrop($id)
    {
        $apple = $this->findAppleById($id);
        if (!$apple || !$apple->canDrop) {
            Yii::$app->session->setFlash('error', 'Что то пошло не так');
            return $this->redirect(['site/index']);
        }
        if (AppleService::dropApple($apple)) {
            Yii::$app->session->setFlash('success', 'Яблоко упало!');
        } else {
            Yii::$app->session->setFlash('error', 'Что то пошло не так');
        }
        return $this->redirect(['site/index']);
    }

    /**
     * Съедаем яблоко c определенным процентом.
     * @param $id
     * @param $percent
     * @return Response
     */
    public function actionEat($id, $percent)
    {
        $apple = $this->findAppleById($id);
        if (!$apple || !$apple->canEat) {
            Yii::$app->session->setFlash('error', 'Что то пошло не так');
            return $this->redirect(['site/index']);
        }
        $result = AppleService::eatApple($apple, $percent);
        Yii::$app->session->setFlash($result['status'], $result['message']);

        return $this->redirect(['site/index']);
    }

    /**
     * Найти яблоко по id.
     * @param $id
     * @return Apple|null
     */
    private function findAppleById($id)
    {
        return Apple::find()
            ->where(['id' => $id])
            ->andWhere(['!=', 'status', Apple::STATUS_DESTROYED_SUCCESSFUL])
            ->one()
        ;
    }

    /**
     * "Ускоритель времени".
     * @return Response
     */
    public function actionTimeBoost()
    {
        $timeBooster = Yii::$app->session->get('timeBooster');
        if (!$timeBooster) {
            $timeBooster = 0;
        }
        $timeBooster ++;
        Yii::$app->session->set('timeBooster', $timeBooster);
        // Проверяем статусы.
        AppleService::checkAppleStatuses();
        Yii::$app->session->setFlash('success', 'Таймер увеличен на 1 час. Статусы обновлены.');
        return $this->redirect(['site/index']);
    }

    /**
     * Сброс ускорителя. ХЗ зачем, пусть будет.
     * @return Response
     */
    public function actionResetBoost()
    {
        Yii::$app->session->set('timeBooster', 0);
        Yii::$app->session->setFlash('success', 'Таймер ускорения сброшен.');
        return $this->redirect(['site/index']);
    }
}