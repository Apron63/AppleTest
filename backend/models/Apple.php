<?php

namespace app\models;

use DateTime;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "apple".
 *
 * @property int $id
 * @property int|null $created_at Дата создания
 * @property int|null $dropped_at Дата падения
 * @property int|null $color Цвет
 * @property int|null $status Статус
 * @property float|null $size Остаток
 */
class Apple extends ActiveRecord
{
    const COLOR_RED = 0;
    const COLOR_YELLOW = 1;
    const COLOR_GREEN = 2;

    const STATUS_IN_TREE = 0;                   // Висит на дереве
    const STATUS_IN_EARTH = 1;                  // Упало на землю
    const STATUS_DESTROYED_SUCCESSFUL = 2;      // Миссия завершена с пользой
    const STATUS_DESTROYED_UNSUCCESSFUL = 3;    // Сгинуло

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apple';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'dropped_at', 'color', 'status'], 'integer'],
            [
                'color',
                'in',
                'range' => [
                    self::COLOR_RED, self::COLOR_YELLOW, self::COLOR_GREEN,
                ]
            ],
            [
                'color', 'default', 'value' => function() {
                    return random_int(self::COLOR_RED, self::COLOR_GREEN);
                }
            ],
            [
                'status',
                'in',
                'range' => [
                    self::STATUS_IN_TREE,
                    self::STATUS_IN_EARTH,
                    self::STATUS_DESTROYED_SUCCESSFUL,
                    self::STATUS_DESTROYED_UNSUCCESSFUL,
                ]
            ],
            ['status', 'default', 'value' => self::STATUS_IN_TREE],
            [
                'size',
                'number',
                'max' => 1.0,
                'tooBig' => 'Недопустимое максимальное значение',
                'min' => 0,
                'tooSmall' => 'Недопустимое минимальное значение',
            ],
            ['size', 'default', 'value' => 1.0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Дата создания',
            'dropped_at' => 'Дата падения',
            'color' => 'Цвет',
            'status' => 'Статус',
            'size' => 'Остаток',
        ];
    }

    /**
     * Переопределим стандартное поведение таймстампа с учетом "ускорителя".
     * @return array|array[]
     */
    public function behaviors()
    {
        $timeBooster = Yii::$app->session->get('timeBooster');
        if (!$timeBooster) {
            $timeBooster = 0;
        }
        $date = new DateTime();
        $date->modify("+$timeBooster hour");
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'value' => $date->getTimestamp(),
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * Получить цвет яблока
     * @return string
     */
    public function getAppleColor()
    {
        switch ($this->color) {
            case self::COLOR_RED:
                return 'red';
            case self::COLOR_YELLOW:
                return 'yellow';
            case self::COLOR_GREEN:
                return 'green';
            default:
                return 'Not defined';
        }
    }

    /**
     * Получить статус яблока.
     * @return string
     */
    public function getAppleStatus()
    {
        switch ($this->status) {
            case self::STATUS_IN_TREE:
                return 'На дереве';
            case self::STATUS_IN_EARTH:
                return 'Упало';
            case self::STATUS_DESTROYED_SUCCESSFUL:
                return 'Съели';
            case self::STATUS_DESTROYED_UNSUCCESSFUL:
                return 'Сгнило';
            default:
                return 'Not defined';
        }
    }

    /**
     * Яблоко можно сбросить.
     * @return bool
     */
    public function getCanDrop()
    {
        return $this->status == self::STATUS_IN_TREE;
    }

    /**
     * Яблоко можно съесть (без учета процента "износа").
     * @return bool
     */
    public function getCanEat()
    {
        return $this->status == self::STATUS_IN_EARTH;
    }
}
