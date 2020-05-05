<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%apple}}`.
 */
class m200502_163128_create_apple_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%apple}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->comment('Дата создания'),
            'dropped_at' => $this->integer()->comment('Дата падения'),
            'color' => $this->tinyInteger()->comment('Цвет'),
            'status' => $this->tinyInteger()->comment('Статус'),
            'size' => $this->float(2)->comment('Остаток'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apple}}');
    }
}
