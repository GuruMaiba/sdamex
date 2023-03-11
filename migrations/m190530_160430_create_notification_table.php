<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%examsection}}`.
 */
class m190530_160430_create_notification_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('notification', [
            'id' => $this->primaryKey(),
            'type' => $this->tinyInteger(2)->notNull(),
            'text' => $this->text()->notNull(), // %username%, %date%
            'create_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('notification_user', [
            'notif_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'view' => $this->boolean()->defaultValue(false),
            'PRIMARY KEY (notif_id,user_id)'
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('notification_user');
        $this->dropTable('notification');
    }
}
