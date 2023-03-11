<?php

use yii\db\Migration;
use mdm\admin\components\Configs;

class m150412_050005_create_user_pay_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('user_pay', [
            'id' => $this->bigPrimaryKey(),                     // id
            'user_id' => $this->integer()->notNull(),           // Пользователь
            'model_id' => $this->integer(),                     // Если есть моделька (Курс или Вебинар)
            'payment_id' => $this->bigInteger(),                // id оплаты в банке
            'type' => $this->tinyInteger(2)->notNull(),         // Тип платежа
            'amount' => $this->integer()->notNull(),            // Стоимость
            'desc' => $this->string(),                          // Описание
            'code' => $this->string(),                          // invite code
            'extra_options' => $this->string(),                 // дополнительные параметры (вероятнее json)
            'success' => $this->boolean()->defaultValue(false), // Завершение оплаты
            'updated_at' => $this->integer()->notNull(),        // Завершение оплаты
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-user_pay-user_id',
            'user_pay',
            'user_id'
        );

        // creates index for column `model_id`
        $this->createIndex(
            'idx-user_pay-model_id',
            'user_pay',
            'model_id'
        );
        // creates index for column `payment_id`
        $this->createIndex(
            'idx-user_pay-payment_id',
            'user_pay',
            'payment_id'
        );
    }

    public function safeDown()
    {
        $this->dropIndex('idx-user_pay-payment_id', 'user_pay');
        $this->dropIndex('idx-user_pay-model_id', 'user_pay');
        $this->dropIndex('idx-user_pay-user_id', 'user_pay');
        $this->dropTable('user_pay');
    }
}
