<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m200502_100336_create_user
 */
class m200502_100336_create_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $user = new User();
        $user->username = 'admin';
        $user->setPassword('admin');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->email = 'test@test.test';
        $user->save(false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200502_100336_create_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200502_100336_create_user cannot be reverted.\n";

        return false;
    }
    */
}
