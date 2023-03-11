<?php

use yii\db\Migration;

/**
 * Handles the creation of table `examtest`.
 */
class m190530_154137_create_examtest_table extends Migration
{
    public function safeUp()
    {
        // TABLE examtest
        $this->createTable('examtest', [
            'id' => $this->primaryKey(),
            'exercise_id' => $this->integer(),
            'lesson_id' => $this->integer(),
            'webinar_id' => $this->integer(),
            'task' => $this->text(),
            'text' => $this->text(),
            'audio_name' => $this->string(),
            'qst_exp' => $this->integer()->defaultValue(1),
            'publish' => $this->boolean()->defaultValue(false),
            'oneshot' => $this->boolean()->defaultValue(false),
            'correct_answers' => $this->text(),
        ]);

        // TABLE examtest_question
        $this->createTable('examtest_question', [
            'id' => $this->primaryKey(),
            'examtest_id' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
            'decision' => $this->text(),
            'multiple_answer' => $this->boolean()->defaultValue(false),
            'hard' => $this->boolean()->defaultValue(false),
            'place' => $this->smallInteger()->notNull(),
        ]);

        // TABLE examtest_answer
        $this->createTable('examtest_answer', [
            'id' => $this->primaryKey(),
            'question_id' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('examtest_answer');
        $this->dropTable('examtest_question');
        $this->dropTable('examtest');
    }
}
