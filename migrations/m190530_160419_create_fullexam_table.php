<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%examsection}}`.
 */
class m190530_160419_create_fullexam_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('fullexam', [
            'id' => $this->primaryKey(),
            'course_id' => $this->integer(),
            'subject_id' => $this->tinyInteger(2)->notNull(),
            'name' => $this->string()->notNull(),
            'desc' => $this->text(),
            'publish' => $this->boolean()->defaultValue(false),
            'max_points' => $this->smallInteger()->defaultValue(0),
            'marks' => $this->string()->notNull(),
            'timer' => $this->smallInteger(5),
        ]);

        $this->createTable('examsection', [
            'id' => $this->primaryKey(),
            'fullexam_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'place' => $this->smallInteger(2)->notNull(),
            'publish' => $this->boolean()->defaultValue(false),
        ]);

        $this->createTable('examsection_exercise', [
            'id' => $this->primaryKey(),
            'section_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'place' => $this->smallInteger(2)->notNull(),
            'type' => $this->smallInteger(1)->notNull(),            // ExamType в папке components
            'task_count' => $this->smallInteger(3)->defaultValue(0),
            'hint' => $this->text(),
            'publish' => $this->boolean()->defaultValue(false),
            'fullexam' => $this->boolean()->defaultValue(false),
            'fullexam_points' => $this->smallInteger(3),
        ]);

        // TABLE examresult_full
        $this->createTable('result_fullexam', [
            'id' => $this->primaryKey(),
            'fullexam_id' => $this->integer()->notNull(),
            // Ученик
            'user_id' => $this->integer()->notNull(),
            // Учитель
            'teacher_id' => $this->integer(),
            'check' => $this->boolean()->defaultValue(false),
            'check_at' => $this->integer(),
            'teacher_comment' => $this->text(),
            // json string [exe_id => exam_id, ...]
            'answers' => $this->text()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('result_fullexam');
        $this->dropTable('examsection_exercise');
        $this->dropTable('examsection');
        $this->dropTable('fullexam');
    }
}
