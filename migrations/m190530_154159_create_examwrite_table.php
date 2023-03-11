<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%examwrite}}`.
 */
class m190530_154159_create_examwrite_table extends Migration
{
    public function safeUp()
    {
        // TABLE examwrite
        $this->createTable('examwrite', [
            'id' => $this->primaryKey(),
            'exercise_id' => $this->integer(),
            'lesson_id' => $this->integer(),
            'webinar_id' => $this->integer(),
            'task' => $this->text(),
            'text' => $this->text(),
            'exp' => $this->integer(),
            'publish' => $this->boolean()->defaultValue(false),
            'audio_name' => $this->string(),
            'themes' => $this->string(),
        ]);

        // TABLE examwrite_answer
        $this->createTable('examwrite_answer', [
            'id' => $this->primaryKey(),
            'examwrite_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'text' => $this->text(),
            'archive_file' => $this->string(),
            'exp' => $this->integer(),
            'points' => $this->smallInteger(3),
            'check' => $this->boolean()->defaultValue(false),
            'check_at' => $this->integer(),
            'teacher_id' => $this->integer(),
            'teacher_comment' => $this->text(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('examwrite_answer');
        $this->dropTable('examwrite');
    }
}
