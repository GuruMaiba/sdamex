<?php

use yii\db\Migration;
use mdm\admin\components\Configs;

class m140506_102207_create_user_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('user', [
            // Идентификатор
            'id' => $this->primaryKey(),
            // Avatar
            'ava' => $this->string()->notNull()->defaultValue('no_img.jpg'),
            // Nickname
            'username' => $this->string(32)->notNull(),
            // E-mail
            'email' => $this->string()->notNull(),
            // Для изменения поля 'email'
            'new_email' => $this->string(),
            // Телефон
            'phone' => $this->string(18),
            // Для изменения поля 'phone'
            'new_phone' => $this->string(18),
            // имя
            'name' => $this->string(30),
            // фамилия
            'surname' => $this->string(30),
            // Дата рождения
            'birthday' => $this->integer(),
            // Пол: 1-мальчик, 0-девочка
            'gender' => $this->boolean(),
            // Фраза пользователя, то, что он хочет сказать
            'phrase' => $this->text(),
            // Логин скайп
            'skype' => $this->string(),
            // авторизационный ключ
            'auth_key' => $this->string(32)->notNull(),
            // хэш пароля
            'password_hash' => $this->string(),
            // token для изменения пароля и E-mail
            'token' => $this->string(),
            // статус пользователя app\components\UserStatus
            'status' => $this->tinyInteger(2)->notNull()->defaultValue(0),
            // дата создания
            'created_at' => $this->integer()->notNull(),
            // дата последнего входа
            'updated_at' => $this->integer()->notNull(),
            // дата важного обновления (для ограничения на 24 часа) 
            'important_updated_at' => $this->integer()->notNull(),
            // количество ошибок при входе
            'count_login_error' => $this->tinyInteger(2),
            // количество доступных занятий с учителем
            'teacher_class' => $this->smallInteger(4)->defaultValue(0),
            // счёт
            'cash' => $this->money(19,0)->defaultValue(0),
            // статистика json
            'statistics' => $this->text(),
            // код по которому пригласили пользователя
            'invite_code' => $this->string(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('user');
    }
}
