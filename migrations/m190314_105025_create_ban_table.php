<?php

use yii\db\Migration;

/**
 * Handles the creation of table `ban`.
 */
class m190314_105025_create_ban_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('ban', [
            // user
            'user_id' => $this->integer()->notNull(),
            'ban_begin' => $this->integer()->notNull(),
            'ban_end' => $this->integer()->notNull(),
            'cause' => $this->text(),
            'PRIMARY KEY(user_id)',
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-ban-user_id',
            'ban',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-ban-user_id',
            'ban',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-ban-user_id', 'ban');
        $this->dropIndex('idx-ban-user_id', 'ban');
        $this->dropTable('ban');
    }
}
