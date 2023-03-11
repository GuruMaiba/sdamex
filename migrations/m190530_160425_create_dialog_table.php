<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%examsection}}`.
 */
class m190530_160425_create_dialog_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('dialog', [
            'id' => $this->primaryKey(),
            'first_user_id' => $this->integer()->notNull(),
            'second_user_id' => $this->integer()->notNull(),
            'last_message_date' => $this->integer(),
            'first_miss_message' => $this->smallInteger(2),
            'second_miss_message' => $this->smallInteger(2),
            'first_del' => $this->boolean()->defaultValue(false),
            'second_del' => $this->boolean()->defaultValue(false),
        ]);

        $this->createTable('dialog_message', [
            'id' => $this->bigPrimaryKey(),
            'dialog_id' => $this->integer()->notNull(),
            'user_id' => $this->integer(),
            'message' => $this->text()->notNull(),
            'date' => $this->integer()->notNull(),
            'view' => $this->boolean()->defaultValue(false),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('dialog_message');
        $this->dropTable('dialog');
    }
}
