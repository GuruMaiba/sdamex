<?php

use yii\db\Migration;

/**
 * Handles the creation of table `course_lesson`.
 */
class m190402_095947_create_course_lesson_table extends Migration
{
    public function safeUp()
    {
        // TABLE course
        $this->createTable('course', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer(),
            'subject_id' => $this->smallInteger(2)->notNull(),
            'ava' => $this->string()->defaultValue('no_img.jpg'),
            'title' => $this->string()->notNull(),
            'desc' => $this->text(),
            'author_about' => $this->text(),
            'cost' => $this->money(19,0)->defaultValue(0),
            'free' => $this->boolean()->defaultValue(false),
            'publish' => $this->boolean()->defaultValue(false),
        ]);

        // TABLE module
        $this->createTable('module', [
            'id' => $this->primaryKey(),
            'course_id' => $this->integer()->notNull(),
            'ava' => $this->string()->defaultValue('no_img.jpg'),
            'title' => $this->string()->notNull(),
            'desc' => $this->text(),
            'place' => $this->smallInteger(2),
            'free' => $this->boolean()->defaultValue(false),
            'publish' => $this->boolean()->defaultValue(false),
        ]);

        // TABLE lesson
        $this->createTable('lesson', [
            'id' => $this->primaryKey(),
            'module_id' => $this->integer()->notNull(),
            'examtest_id' => $this->integer(),
            'examwrite_id' => $this->integer(),
            'video' => $this->string()->notNull(),
            'title' => $this->string()->notNull(),
            'desc' => $this->text(),
            'links' => $this->text(),
            'place' => $this->smallInteger(3),
            'free' => $this->boolean()->defaultValue(false),
            'publish' => $this->boolean()->defaultValue(false),
        ]);

        // TABLE learner_course
        $this->createTable('learner_course', [
            'learner_id' => $this->integer()->notNull(),
            'course_id' => $this->integer()->notNull(),
            'start_at' => $this->integer()->notNull(),
            'end_at' => $this->integer()->notNull(),
            'PRIMARY KEY (learner_id,course_id)'
        ]);

        // TABLE course
        $this->createTable('course_question', [
            'id' => $this->primaryKey(),
            'learner_id' => $this->integer()->notNull(),
            'course_id' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
            'teacher_id' => $this->integer(),
            'answer' => $this->text(),
        ]);

        // TABLE course_webinar
        $this->createTable('course_webinar', [
            'course_id' => $this->integer()->notNull(),
            'webinar_id' => $this->integer()->notNull(),
            'PRIMARY KEY (course_id, webinar_id)'
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('course_webinar');
        $this->dropTable('course_question');
        $this->dropTable('learner_course');
        $this->dropTable('lesson');
        $this->dropTable('module');
        $this->dropTable('course');
    }
}
