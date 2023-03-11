<?php

use yii\db\Migration;
use mdm\admin\components\Configs;

class m150413_050000_create_teacher_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('teacher', [
            'user_id' => $this->integer()->notNull(),
            // титул
            'dignity' => $this->string()->defaultValue('Новый учитель'),
            // Видео визитка
            'video' => $this->string(),
            // о себе
            'about_me' => $this->text(),
            // рейтинг
            'rating' => $this->decimal(2, 1)->defaultValue(0),
            // количество часов, за которое блокировать время
            'time_lock' => $this->smallInteger(3)->defaultValue(6),
            // предметы json
            'subjects' => $this->string(),
            // специализация json
            'specialization' => $this->string(),
            // количество учеников
            'student_count' => $this->smallInteger()->defaultValue(0),
            'PRIMARY KEY(user_id)',
        ]);

        $this->createTable('teacher_time', [
            'id' => $this->primaryKey(),
            'teacher_id' => $this->integer()->notNull(),
            'student_id' => $this->integer(),
            'day' => $this->smallInteger(1)->notNull(),
            'hour' => $this->smallInteger(2)->notNull(),
            'min' => $this->smallInteger(2)->notNull(),
        ]);

        $this->createTable('teacher_time_edit', [
            'id' => $this->primaryKey(),
            'teacher_id' => $this->integer()->notNull(),
            'date' => $this->integer()->notNull(),
            // add = true, del = false
            'add_or_del' => $this->boolean()->notNull(),
        ]);

        $this->createTable('teacher_appointment', [
            'archive_id' => $this->integer()->notNull(),
            'teacher_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'subject_id' => $this->smallInteger(2)->notNull(),
            'begin_date' => $this->integer()->notNull(),
            'end_date' => $this->integer()->notNull(),
            'status' => $this->smallInteger(1)->defaultValue(0),
            'PRIMARY KEY(archive_id)',
        ]);

        $this->createTable('teacher_appointment_archive', [
            'id' => $this->primaryKey(),
            'teacher_id' => $this->integer(),
            'student_id' => $this->integer(),
            'subject_id' => $this->smallInteger(2)->notNull(),
            'begin_date' => $this->integer()->notNull(),
            'end_date' => $this->integer()->notNull(),
            'status' => $this->smallInteger(1)->defaultValue(0),
            'teacher_message' => $this->text(),
            'student_message' => $this->text(),
        ]);

        $this->createTable('teacher_student', [
            'teacher_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'is_review' => $this->boolean()->defaultValue(false),
            'review_text' => $this->text(),
            'review_rating' => $this->smallInteger(1),
            'review_date' => $this->integer(),
            'review_anonymously' => $this->integer(),
            'PRIMARY KEY(teacher_id, student_id)',
        ]);
    }

    public function safeDown()
    {
        // TEACHER
        $this->dropTable('teacher_student');
        $this->dropTable('teacher_appointment_archive');
        $this->dropTable('teacher_appointment');
        $this->dropTable('teacher_time_edit');
        $this->dropTable('teacher_time');
        $this->dropTable('teacher');
    }
}
