<?php

use yii\db\Migration;

/**
 * Handles the creation of table `webinar`.
 */
class m190403_062332_create_webinar_table extends Migration
{
    public function safeUp()
    {
        // TABLE webinar
        $this->createTable('webinar', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer(),
            'examtest_id' => $this->integer(),
            'examwrite_id' => $this->integer(),
            'subject_id' => $this->smallInteger(2)->notNull(),
            'ava' => $this->string(),
            'live_link' => $this->string(),
            'video_link' => $this->string(),
            'title' => $this->string()->notNull(),
            'desc' => $this->text(),
            'links' => $this->text(),
            'complexity' => $this->tinyInteger(1),
            'cost' => $this->money(19,0)->defaultValue(0),
            'publish' => $this->boolean()->defaultValue(false),
            'start' => $this->boolean()->defaultValue(false),
            'end' => $this->boolean()->defaultValue(false),
            'start_at' => $this->integer()->notNull(),
            'views' => $this->integer()->defaultValue(0),
        ]);

        // TABLE webinar_member
        $this->createTable('webinar_member', [
            'webinar_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'progress' => $this->string(),
            'PRIMARY KEY (webinar_id,user_id)'
        ]);

        // TABLE webinar_comment
        $this->createTable('webinar_comment', [
            'id' => $this->primaryKey(),
            'webinar_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'reply_id' => $this->integer()->defaultValue(0),
            'message' => $this->text()->notNull(),
            'create_at' => $this->integer()->notNull(),
        ]);

        // TABLE webinar_chat
        $this->createTable('webinar_chat', [
            'id' => $this->bigPrimaryKey(),
            'webinar_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'message' => $this->text()->notNull(),
        ]);

        // TABLE webinar_chat_ban
        $this->createTable('webinar_chat_ban', [
            'webinar_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'PRIMARY KEY (webinar_id,user_id)'
        ]);

        // TABLE webinar_tag
        $this->createTable('wtag', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        // TABLE webinar_wtag
        $this->createTable('webinar_wtag', [
            'webinar_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
            'PRIMARY KEY (webinar_id,tag_id)'
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('webinar_wtag');
        $this->dropTable('wtag');
        $this->dropTable('webinar_chat_ban');
        $this->dropTable('webinar_chat');
        $this->dropTable('webinar_comment');
        $this->dropTable('webinar_member');
        $this->dropTable('webinar');
    }
}
