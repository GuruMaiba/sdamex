<?php

use yii\db\Migration;
use mdm\admin\components\Configs;

class m150412_050000_create_user_lvl_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('user_lvl', [
            'user_id' => $this->integer()->notNull(),           // id
            'subject_id' => $this->smallInteger(2)->notNull(),  // Предмет
            'exp' => $this->integer()->defaultValue(0),         // Опыт
            'lvl' => $this->smallInteger(4)->defaultValue(1),   // Уровень
            'PRIMARY KEY(user_id, subject_id)',
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-user_lvl-user_id',
            'user_lvl',
            'user_id'
        );

        // FOREIGN KEY for user_id
        $this->addForeignKey(
            'fk-user_lvl-user_id',
            'user_lvl',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `subject_id`
        $this->createIndex(
            'idx-user_lvl-subject_id',
            'user_lvl',
            'subject_id'
        );
    }

    public function safeDown()
    {
        $this->dropIndex('idx-user_lvl-subject_id', 'user_lvl');
        $this->dropForeignKey('fk-user_lvl-user_id', 'user_lvl');
        $this->dropIndex('idx-user_lvl-user_id', 'user_lvl');
        $this->dropTable('user_lvl');
    }
}
