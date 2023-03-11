<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_social`.
 */
class m140506_102307_create_user_social_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('user_social', [
            'social_id' => $this->string()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->string(2)->notNull(),
            'PRIMARY KEY(social_id)',
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-user_social-user_id',
            'user_social',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-user_social-user_id',
            'user_social',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-user_social-user_id', 'user_social');
        $this->dropIndex('idx-user_social-user_id', 'user_social');
        $this->dropTable('user_social');
    }
}
