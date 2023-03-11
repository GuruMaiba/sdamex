<?php

use yii\db\Migration;

/**
 * Class m190530_160600_create_all_foreignkey
 */
class m190530_160600_create_all_foreignkey extends Migration
{
    public function safeUp()
    {
        //////////////////////////////////////////
        // PROMOTER CODE
        ///////-----------------------------------

        // creates index for column `promoter_id`
        $this->createIndex(
            'idx-promoter_code-promoter_id',
            'promoter_code',
            'promoter_id'
        );

        // add foreign key for table `promoter_id`
        $this->addForeignKey(
            'fk-promoter_code-promoter_id',
            'promoter_code',
            'promoter_id',
            'user',
            'id',
            'CASCADE'
        );

        ///////-----------------------------------
        // END PROMOTER CODE

        //////////////////////////////////////////
        // TEACHER
        ///////-----------------------------------

        // INDEX user_id
        $this->createIndex(
            'idx-teacher-user_id',
            'teacher',
            'user_id'
        );

        // FOREIGN KEY for user_id
        $this->addForeignKey(
            'fk-teacher-user_id',
            'teacher',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // TEACHER_TIME
        // ------------------------

        // INDEX teacher_id
        $this->createIndex(
            'idx-teacher_time-teacher_id',
            'teacher_time',
            'teacher_id'
        );

        // FOREIGN KEY for teacher_id
        $this->addForeignKey(
            'fk-teacher_time-teacher_id',
            'teacher_time',
            'teacher_id',
            'teacher',
            'user_id',
            'CASCADE'
        );

        // INDEX student_id
        $this->createIndex(
            'idx-teacher_time-student_id',
            'teacher_time',
            'student_id'
        );

        // FOREIGN KEY for student_id
        $this->addForeignKey(
            'fk-teacher_time-student_id',
            'teacher_time',
            'student_id',
            'user',
            'id',
            'SET NULL'
        );

        // TEACHER_TIME_EDIT
        // ------------------------

        // INDEX teacher_id
        $this->createIndex(
            'idx-teacher_time_edit-teacher_id',
            'teacher_time_edit',
            'teacher_id'
        );

        // FOREIGN KEY for teacher_id
        $this->addForeignKey(
            'fk-teacher_time_edit-teacher_id',
            'teacher_time_edit',
            'teacher_id',
            'teacher',
            'user_id',
            'CASCADE'
        );

        // TEACHER_APPOINTMENT
        // ------------------------

        // INDEX archive_id
        $this->createIndex(
            'idx-teacher_appointment-archive_id',
            'teacher_appointment',
            'archive_id'
        );

        // FOREIGN KEY for archive_id
        $this->addForeignKey(
            'fk-teacher_appointment-archive_id',
            'teacher_appointment',
            'archive_id',
            'teacher_appointment_archive',
            'id',
            'CASCADE'
        );

        // INDEX teacher_id
        $this->createIndex(
            'idx-teacher_appointment-teacher_id',
            'teacher_appointment',
            'teacher_id'
        );

        // FOREIGN KEY for teacher_id
        $this->addForeignKey(
            'fk-teacher_appointment-teacher_id',
            'teacher_appointment',
            'teacher_id',
            'teacher',
            'user_id',
            'CASCADE'
        );

        // INDEX student_id
        $this->createIndex(
            'idx-teacher_appointment-student_id',
            'teacher_appointment',
            'student_id'
        );

        // FOREIGN KEY for student_id
        $this->addForeignKey(
            'fk-teacher_appointment-student_id',
            'teacher_appointment',
            'student_id',
            'user',
            'id',
            'CASCADE'
        );

        // TEACHER_APPOINTMENT_ARCHIVE
        // ------------------------

        // INDEX teacher_id
        $this->createIndex(
            'idx-teacher_appointment_archive-teacher_id',
            'teacher_appointment_archive',
            'teacher_id'
        );

        // FOREIGN KEY for teacher_id
        $this->addForeignKey(
            'fk-teacher_appointment_archive-teacher_id',
            'teacher_appointment_archive',
            'teacher_id',
            'teacher',
            'user_id',
            'SET NULL'
        );

        // INDEX student_id
        $this->createIndex(
            'idx-teacher_appointment_archive-student_id',
            'teacher_appointment_archive',
            'student_id'
        );

        // FOREIGN KEY for student_id
        $this->addForeignKey(
            'fk-teacher_appointment_archive-student_id',
            'teacher_appointment_archive',
            'student_id',
            'user',
            'id',
            'SET NULL'
        );

        // TEACHER_STUDENT
        // ------------------------

        // INDEX teacher_id
        $this->createIndex(
            'idx-teacher_student-teacher_id',
            'teacher_student',
            'teacher_id'
        );

        // FOREIGN KEY for teacher_id
        $this->addForeignKey(
            'fk-teacher_student-teacher_id',
            'teacher_student',
            'teacher_id',
            'teacher',
            'user_id',
            'CASCADE'
        );

        // INDEX student_id
        $this->createIndex(
            'idx-teacher_student-student_id',
            'teacher_student',
            'student_id'
        );

        // FOREIGN KEY for student_id
        $this->addForeignKey(
            'fk-teacher_student-student_id',
            'teacher_student',
            'student_id',
            'user',
            'id',
            'CASCADE'
        );

        ///////-----------------------------------
        // END TEACHER


        //////////////////////////////////////////
        // COURSE
        ///////-----------------------------------

        // INDEX subject_id
        $this->createIndex(
            'idx-course-subject_id',
            'course',
            'subject_id'
        );

        // INDEX author_id
        $this->createIndex(
            'idx-course-author_id',
            'course',
            'author_id'
        );

        // FOREIGN KEY author_id
        $this->addForeignKey(
            'fk-course-author_id',
            'course',
            'author_id',
            'user',
            'id',
            'SET NULL'
        );

        // MODULES
        // ------------------------------------------

        // INDEX course_id
        $this->createIndex(
            'idx-module-course_id',
            'module',
            'course_id'
        );

        // FOREIGN KEY course_id
        $this->addForeignKey(
            'fk-module-course_id',
            'module',
            'course_id',
            'course',
            'id',
            'CASCADE'
        );

        // LESSONS
        // ------------------------------------------

        // INDEX module_id
        $this->createIndex(
            'idx-lesson-module_id',
            'lesson',
            'module_id'
        );

        // FOREIGN KEY module_id
        $this->addForeignKey(
            'fk-lesson-module_id',
            'lesson',
            'module_id',
            'module',
            'id',
            'CASCADE'
        );

        // INDEX examtest_id
        $this->createIndex(
            'idx-lesson-examtest_id',
            'lesson',
            'examtest_id'
        );

        // FOREIGN KEY examtest_id
        $this->addForeignKey(
            'fk-lesson-examtest_id',
            'lesson',
            'examtest_id',
            'examtest',
            'id',
            'SET NULL'
        );

        // INDEX examwrite_id
        $this->createIndex(
            'idx-lesson-examwrite_id',
            'lesson',
            'examwrite_id'
        );

        // FOREIGN KEY examwrite_id
        $this->addForeignKey(
            'fk-lesson-examwrite_id',
            'lesson',
            'examwrite_id',
            'examwrite',
            'id',
            'SET NULL'
        );

        // LEARNER_COURSE
        // ------------------------------------------

        // INDEX learner_id
        $this->createIndex(
            'idx-learner_course-learner_id',
            'learner_course',
            'learner_id'
        );

        // FOREIGN KEY learner_id
        $this->addForeignKey(
            'fk-learner_course-learner_id',
            'learner_course',
            'learner_id',
            'user',
            'id',
            'CASCADE'
        );

        // INDEX course_id
        $this->createIndex(
            'idx-learner_course-course_id',
            'learner_course',
            'course_id'
        );

        // FOREIGN KEY course_id
        $this->addForeignKey(
            'fk-learner_course-course_id',
            'learner_course',
            'course_id',
            'course',
            'id',
            'CASCADE'
        );

        // COURSE_QUESTION
        // ------------------------------------------

        // INDEX learner_id
        $this->createIndex(
            'idx-course_question-learner_id',
            'course_question',
            'learner_id'
        );

        // FOREIGN KEY learner_id
        $this->addForeignKey(
            'fk-course_question-learner_id',
            'course_question',
            'learner_id',
            'user',
            'id',
            'CASCADE'
        );

        // INDEX course_id
        $this->createIndex(
            'idx-course_question-course_id',
            'course_question',
            'course_id'
        );

        // FOREIGN KEY course_id
        $this->addForeignKey(
            'fk-course_question-course_id',
            'course_question',
            'course_id',
            'course',
            'id',
            'CASCADE'
        );

        // COURSE_WEBINAR
        // ------------------------------------------

        // INDEX webinar_id
        $this->createIndex(
            'idx-course_webinar-webinar_id',
            'course_webinar',
            'webinar_id'
        );

        // FOREIGN KEY webinar_id
        $this->addForeignKey(
            'fk-course_webinar-webinar_id',
            'course_webinar',
            'webinar_id',
            'webinar',
            'id',
            'CASCADE'
        );

        // INDEX course_id
        $this->createIndex(
            'idx-course_webinar-course_id',
            'course_webinar',
            'course_id'
        );

        // FOREIGN KEY course_id
        $this->addForeignKey(
            'fk-course_webinar-course_id',
            'course_webinar',
            'course_id',
            'course',
            'id',
            'CASCADE'
        );

        ///////-----------------------------------
        // END COURSE


        //////////////////////////////////////////
        // WEBINAR
        ///////-----------------------------------

        // INDEX author_id
        $this->createIndex(
            'idx-webinar-author_id',
            'webinar',
            'author_id'
        );

        // FOREIGN KEY author_id
        $this->addForeignKey(
            'fk-webinar-author_id',
            'webinar',
            'author_id',
            'user',
            'id',
            'SET NULL'
        );

        // INDEX examtest_id
        $this->createIndex(
            'idx-webinar-examtest_id',
            'webinar',
            'examtest_id'
        );

        // FOREIGN KEY examtest_id
        $this->addForeignKey(
            'fk-webinar-examtest_id',
            'webinar',
            'examtest_id',
            'examtest',
            'id',
            'SET NULL'
        );

        // INDEX examwrite_id
        $this->createIndex(
            'idx-webinar-examwrite_id',
            'webinar',
            'examwrite_id'
        );

        // FOREIGN KEY examwrite_id
        $this->addForeignKey(
            'fk-webinar-examwrite_id',
            'webinar',
            'examwrite_id',
            'examwrite',
            'id',
            'SET NULL'
        );

        // WEBINAR_MEMBER
        // ------------------------

        // INDEX webinar_id
        $this->createIndex(
            'idx-webinar_member-webinar_id',
            'webinar_member',
            'webinar_id'
        );

        // FOREIGN KEY webinar_id
        $this->addForeignKey(
            'fk-webinar_member-webinar_id',
            'webinar_member',
            'webinar_id',
            'webinar',
            'id',
            'CASCADE'
        );

        // INDEX user_id
        $this->createIndex(
            'idx-webinar_member-user_id',
            'webinar_member',
            'user_id'
        );

        // FOREIGN KEY user_id
        $this->addForeignKey(
            'fk-webinar_member-user_id',
            'webinar_member',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // WEBINAR_COMMENT
        // ------------------------

        // INDEX webinar_id
        $this->createIndex(
            'idx-webinar_comment-webinar_id',
            'webinar_comment',
            'webinar_id'
        );

        // FOREIGN KEY webinar_id
        $this->addForeignKey(
            'fk-webinar_comment-webinar_id',
            'webinar_comment',
            'webinar_id',
            'webinar',
            'id',
            'CASCADE'
        );

        // INDEX user_id
        $this->createIndex(
            'idx-webinar_comment-user_id',
            'webinar_comment',
            'user_id'
        );

        // FOREIGN KEY user_id
        $this->addForeignKey(
            'fk-webinar_comment-user_id',
            'webinar_comment',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // INDEX reply_id
        $this->createIndex(
            'idx-webinar_comment-reply_id',
            'webinar_comment',
            'reply_id'
        );

        // WEBINAR_CHAT
        // ------------------------

        // INDEX webinar_id
        $this->createIndex(
            'idx-webinar_chat-webinar_id',
            'webinar_chat',
            'webinar_id'
        );

        // FOREIGN KEY webinar_id
        $this->addForeignKey(
            'fk-webinar_chat-webinar_id',
            'webinar_chat',
            'webinar_id',
            'webinar',
            'id',
            'CASCADE'
        );

        // INDEX user_id
        $this->createIndex(
            'idx-webinar_chat-user_id',
            'webinar_chat',
            'user_id'
        );

        // WEBINAR_CHAT_BAN
        // ------------------------

        // INDEX webinar_id
        $this->createIndex(
            'idx-webinar_chat_ban-webinar_id',
            'webinar_chat_ban',
            'webinar_id'
        );

        // FOREIGN KEY webinar_id
        $this->addForeignKey(
            'fk-webinar_chat_ban-webinar_id',
            'webinar_chat_ban',
            'webinar_id',
            'webinar',
            'id',
            'CASCADE'
        );

        // INDEX user_id
        $this->createIndex(
            'idx-webinar_chat_ban-user_id',
            'webinar_chat_ban',
            'user_id'
        );

        ///////-----------------------------------
        // END WEBINAR


        //////////////////////////////////////////
        // THEME
        ///////-----------------------------------

        // THEME_WEBINAR
        // ------------------------

        // INDEX theme_id
        $this->createIndex(
            'idx-theme_webinar-theme_id',
            'theme_webinar',
            'theme_id'
        );

        // FOREIGN KEY for theme_id
        $this->addForeignKey(
            'fk-theme_webinar-theme_id',
            'theme_webinar',
            'theme_id',
            'theme',
            'id',
            'CASCADE'
        );

        // INDEX webinar_id
        $this->createIndex(
            'idx-theme_webinar-webinar_id',
            'theme_webinar',
            'webinar_id'
        );

        // FOREIGN KEY for webinar_id
        $this->addForeignKey(
            'fk-theme_webinar-webinar_id',
            'theme_webinar',
            'webinar_id',
            'webinar',
            'id',
            'CASCADE'
        );

        // THEME_LESSON
        // ------------------------

        // INDEX theme_id
        $this->createIndex(
            'idx-theme_lesson-theme_id',
            'theme_lesson',
            'theme_id'
        );

        // FOREIGN KEY for theme_id
        $this->addForeignKey(
            'fk-theme_lesson-theme_id',
            'theme_lesson',
            'theme_id',
            'theme',
            'id',
            'CASCADE'
        );

        // INDEX lesson_id
        $this->createIndex(
            'idx-theme_lesson-lesson_id',
            'theme_lesson',
            'lesson_id'
        );

        // FOREIGN KEY for lesson_id
        $this->addForeignKey(
            'fk-theme_lesson-lesson_id',
            'theme_lesson',
            'lesson_id',
            'lesson',
            'id',
            'CASCADE'
        );

        ///////-----------------------------------
        // END THEME


        //////////////////////////////////////////
        // DIALOG
        ///////-----------------------------------

        // INDEX first_user_id
        $this->createIndex(
            'idx-dialog-first_user_id',
            'dialog',
            'first_user_id'
        );

        // FOREIGN KEY for first_user_id
        $this->addForeignKey(
            'fk-dialog-first_user_id',
            'dialog',
            'first_user_id',
            'user',
            'id',
            'CASCADE'
        );

        // INDEX second_user_id
        $this->createIndex(
            'idx-dialog-second_user_id',
            'dialog',
            'second_user_id'
        );

        // FOREIGN KEY for second_user_id
        $this->addForeignKey(
            'fk-dialog-second_user_id',
            'dialog',
            'second_user_id',
            'user',
            'id',
            'CASCADE'
        );

        // DIALOG_MESSAGE
        // ------------------------

        // INDEX dialog_id
        $this->createIndex(
            'idx-dialog_message-dialog_id',
            'dialog_message',
            'dialog_id'
        );

        // FOREIGN KEY for dialog_id
        $this->addForeignKey(
            'fk-dialog_message-dialog_id',
            'dialog_message',
            'dialog_id',
            'dialog',
            'id',
            'CASCADE'
        );

        // INDEX user_id
        $this->createIndex(
            'idx-dialog_message-user_id',
            'dialog_message',
            'user_id'
        );

        // FOREIGN KEY for user_id
        $this->addForeignKey(
            'fk-dialog_message-user_id',
            'dialog_message',
            'user_id',
            'user',
            'id',
            'SET NULL'
        );

        ///////-----------------------------------
        // END DIALOG

        //////////////////////////////////////////
        // NOTIFICATION
        ///////-----------------------------------

        // NOTIFICATION_USER
        // ------------------------

        // INDEX notif_id
        $this->createIndex(
            'idx-notification_user-notif_id',
            'notification_user',
            'notif_id'
        );

        // FOREIGN KEY for notif_id
        $this->addForeignKey(
            'fk-notification_user-notif_id',
            'notification_user',
            'notif_id',
            'notification',
            'id',
            'CASCADE'
        );

        // INDEX user_id
        $this->createIndex(
            'idx-notification_user-user_id',
            'notification_user',
            'user_id'
        );

        // FOREIGN KEY for user_id
        $this->addForeignKey(
            'fk-notification_user-user_id',
            'notification_user',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        ///////-----------------------------------
        // END NOTIFICATION
    }

    public function safeDown()
    {
        // NOTIFICATION
        // ---------------------------------------------------

        // NOTIFICATION_USER
        $this->dropForeignKey('fk-notification_user-notif_id', 'notification_user'); // NOTIF_ID
        $this->dropIndex('idx-notification_user-notif_id', 'notification_user');
        $this->dropForeignKey('fk-notification_user-user_id', 'notification_user');  // USER_ID
        $this->dropIndex('idx-notification_user-user_id', 'notification_user');

        ////// END NOTIFICATION

        ///////////////////////////////////////////////

        // DIALOG
        // ---------------------------------------------------

        // DIALOG_MESSAGE
        $this->dropForeignKey('fk-dialog_message-user_id', 'dialog_message'); // USER_ID
        $this->dropIndex('idx-dialog_message-user_id', 'dialog_message');
        $this->dropForeignKey('fk-dialog_message-dialog_id', 'dialog_message'); // DIALOG_ID
        $this->dropIndex('idx-dialog_message-dialog_id', 'dialog_message');

        // MAIN
        $this->dropForeignKey('fk-dialog-second_user_id', 'dialog'); // SECOND_USER_ID
        $this->dropIndex('idx-dialog-second_user_id', 'dialog');
        $this->dropForeignKey('fk-dialog-first_user_id', 'dialog'); // FIRST_USER_ID
        $this->dropIndex('idx-dialog-first_user_id', 'dialog');

        ////// END DIALOG

        ///////////////////////////////////////////////

        // THEME
        // ---------------------------------------------------

        // THEME_LESSON
        $this->dropForeignKey('fk-theme_lesson-theme_id', 'theme_lesson');      // THEME_ID
        $this->dropIndex('idx-theme_lesson-theme_id', 'theme_lesson');
        $this->dropForeignKey('fk-theme_lesson-lesson_id', 'theme_lesson');     // LESSON_ID
        $this->dropIndex('idx-theme_lesson-lesson_id', 'theme_lesson');

        // THEME_WEBINAR
        $this->dropForeignKey('fk-theme_webinar-theme_id', 'theme_webinar');    // THEME_ID
        $this->dropIndex('idx-theme_webinar-theme_id', 'theme_webinar');
        $this->dropForeignKey('fk-theme_webinar-webinar_id', 'theme_webinar');  // WEBINAR_ID
        $this->dropIndex('idx-theme_webinar-webinar_id', 'theme_webinar');

        ////// END THEME

        ///////////////////////////////////////////////

        // WEBINAR
        // ---------------------------------------------------
        $this->dropForeignKey('fk-webinar-examwrite_id', 'webinar');    // EXAMWRITE_ID
        $this->dropIndex('idx-webinar-examwrite_id', 'webinar');
        $this->dropForeignKey('fk-webinar-examtest_id', 'webinar');     // EXAMTEST_ID
        $this->dropIndex('idx-webinar-examtest_id', 'webinar');
        $this->dropForeignKey('fk-webinar-author_id', 'webinar');       // USER_ID
        $this->dropIndex('idx-webinar-author_id', 'webinar');

        // WEBINAR_MEMBER
        $this->dropForeignKey('fk-webinar_member-webinar_id', 'webinar_member');    // WEBINAR_ID
        $this->dropIndex('idx-webinar_member-webinar_id', 'webinar_member');
        $this->dropForeignKey('fk-webinar_member-user_id', 'webinar_member');       // USER_ID
        $this->dropIndex('idx-webinar_member-user_id', 'webinar_member');

        // WEBINAR_COMMENT
        $this->dropForeignKey('fk-webinar_comment-webinar_id', 'webinar_comment');  // WEBINAR_ID
        $this->dropIndex('idx-webinar_comment-webinar_id', 'webinar_comment');
        $this->dropForeignKey('fk-webinar_comment-user_id', 'webinar_comment');     // USER_ID
        $this->dropIndex('idx-webinar_comment-user_id', 'webinar_comment');
        $this->dropIndex('idx-webinar_comment-reply_id', 'webinar_comment');        // REPLY_ID

        // WEBINAR_CHAT
        $this->dropForeignKey('fk-webinar_chat-webinar_id', 'webinar_chat');  // WEBINAR_ID
        $this->dropIndex('idx-webinar_chat-webinar_id', 'webinar_chat');
        $this->dropIndex('idx-webinar_chat-user_id', 'webinar_chat');         // USER_ID

        // WEBINAR_CHAT_BAN
        $this->dropForeignKey('fk-webinar_chat_ban-webinar_id', 'webinar_chat_ban');  // WEBINAR_ID
        $this->dropIndex('idx-webinar_chat_ban-webinar_id', 'webinar_chat_ban');
        $this->dropIndex('idx-webinar_chat_ban-user_id', 'webinar_chat_ban');         // USER_ID

        ////// END WEBINAR

        ///////////////////////////////////////////////

        // COURSE
        // ---------------------------------------------------

        // WEBINAR
        // -------------------------
        $this->dropForeignKey('fk-course_webinar-webinar_id', 'course_webinar');    // WEBINAR_ID
        $this->dropIndex('idx-course_webinar-webinar_id', 'course_webinar');
        $this->dropForeignKey('fk-course_webinar-course_id', 'course_webinar');     // COURSE_ID
        $this->dropIndex('idx-course_webinar-course_id', 'course_webinar');

        // QUESTIONS
        // -------------------------
        $this->dropForeignKey('fk-course_question-course_id', 'course_question');   // COURSE_ID
        $this->dropIndex('idx-course_question-course_id', 'course_question');
        $this->dropForeignKey('fk-course_question-learner_id', 'course_question');  // LEARNER_ID
        $this->dropIndex('idx-course_question-learner_id', 'course_question');

        // LEARNER
        // -------------------------
        $this->dropForeignKey('fk-learner_course-course_id', 'learner_course');     // COURSE_ID
        $this->dropIndex('idx-learner_course-course_id', 'learner_course');
        $this->dropForeignKey('fk-learner_course-learner_id', 'learner_course');    // LEARNER_ID
        $this->dropIndex('idx-learner_course-learner_id', 'learner_course');

        // LESSON
        // --------------------------
        $this->dropForeignKey('fk-lesson-examwrite_id', 'lesson');  // EXAMWRITE_ID
        $this->dropIndex('idx-lesson-examwrite_id', 'lesson');
        $this->dropForeignKey('fk-lesson-examtest_id', 'lesson');   // EXAMTEST_ID
        $this->dropIndex('idx-lesson-examtest_id', 'lesson');
        $this->dropForeignKey('fk-lesson-module_id', 'lesson');     // MODULE_ID
        $this->dropIndex('idx-lesson-module_id', 'lesson');

        // MODULE
        // --------------------------
        $this->dropForeignKey('fk-module-course_id', 'module');     // COURSE_ID
        $this->dropIndex('idx-module-course_id', 'module');
        // $this->dropForeignKey('fk-module-author_id', 'module');  // AUTHOR_ID
        // $this->dropIndex('idx-module-author_id', 'module');
        // $this->dropIndex('idx-module-subject_id', 'module');     // SUBJECT_ID

        ////// END COURSE

        ///////////////////////////////////////////////

        // TEACHER
        // ---------------------------------------------------

        // TEACHER_STUDENT
        $this->dropForeignKey('fk-teacher_student-student_id', 'teacher_student');  // STUDENT_ID
        $this->dropIndex('idx-teacher_student-student_id', 'teacher_student');
        $this->dropForeignKey('fk-teacher_student-teacher_id', 'teacher_student');  // TEACHER_ID
        $this->dropIndex('idx-teacher_student-teacher_id', 'teacher_student');

        // TEACHER_APPOINTMENT_ARCHIVE
        $this->dropForeignKey('fk-teacher_appointment_archive-student_id', 'teacher_appointment_archive');  // STUDENT_ID
        $this->dropIndex('idx-teacher_appointment_archive-student_id', 'teacher_appointment_archive');
        $this->dropForeignKey('fk-teacher_appointment_archive-teacher_id', 'teacher_appointment_archive');  // TEACHER_ID
        $this->dropIndex('idx-teacher_appointment_archive-teacher_id', 'teacher_appointment_archive');

        // TEACHER_APPOINTMENT
        $this->dropForeignKey('fk-teacher_appointment-student_id', 'teacher_appointment');  // STUDENT_ID
        $this->dropIndex('idx-teacher_appointment-student_id', 'teacher_appointment');
        $this->dropForeignKey('fk-teacher_appointment-teacher_id', 'teacher_appointment');  // TEACHER_ID
        $this->dropIndex('idx-teacher_appointment-teacher_id', 'teacher_appointment');
        $this->dropForeignKey('fk-teacher_appointment-archive_id', 'teacher_appointment');  // ARCHIVE_ID
        $this->dropIndex('idx-teacher_appointment-archive_id', 'teacher_appointment');
        $this->dropForeignKey('fk-teacher_time_edit-teacher_id', 'teacher_time_edit');      // TEACHER_ID
        $this->dropIndex('idx-teacher_time_edit-teacher_id', 'teacher_time_edit');

        // TEACHER_TIME
        $this->dropForeignKey('fk-teacher_time-student_id', 'teacher_time');    // STUDENT_ID
        $this->dropIndex('idx-teacher_time-student_id', 'teacher_time');
        $this->dropForeignKey('fk-teacher_time-teacher_id', 'teacher_time');    // TEACHER_ID
        $this->dropIndex('idx-teacher_time-teacher_id', 'teacher_time');

        // USER_ID
        $this->dropForeignKey('fk-teacher-user_id', 'teacher');
        $this->dropIndex('idx-teacher-user_id', 'teacher');

        ////// END TEACHER

        ///////////////////////////////////////////////

        // PROMOTER CODE
        // ---------------------------------------------------

        $this->dropForeignKey('fk-promoter_code-promoter_id', 'promoter_code');
        $this->dropIndex('idx-promoter_code-promoter_id', 'promoter_code');

        ////// END PROMOTER CODE
    }
}
