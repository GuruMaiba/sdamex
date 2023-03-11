<?php

use yii\db\Migration;

/**
 * Class m190530_160621_create_exam_foreignkey
 */
class m190530_160621_create_exam_foreignkey extends Migration
{
    public function safeUp()
    {
        //////////////////////////////////////////
        // EXAMTEST
        ///////-----------------------------------

        // INDEX exercise_id
        $this->createIndex(
            'idx-examtest-exercise_id',
            'examtest',
            'exercise_id'
        );

        // FOREIGN KEY for exercise_id
        $this->addForeignKey(
            'fk-examtest-exercise_id',
            'examtest',
            'exercise_id',
            'examsection_exercise',
            'id',
            'CASCADE'
        );

        // --------------------

        // INDEX lesson_id
        $this->createIndex(
            'idx-examtest-lesson_id',
            'examtest',
            'lesson_id'
        );

        // FOREIGN KEY for lesson_id
        $this->addForeignKey(
            'fk-examtest-lesson_id',
            'examtest',
            'lesson_id',
            'lesson',
            'id',
            'CASCADE'
        );

        // --------------------

        // INDEX webinar_id
        $this->createIndex(
            'idx-examtest-webinar_id',
            'examtest',
            'webinar_id'
        );

        // FOREIGN KEY for webinar_id
        $this->addForeignKey(
            'fk-examtest-webinar_id',
            'examtest',
            'webinar_id',
            'webinar',
            'id',
            'CASCADE'
        );

        // QUESTIONS
        // ------------------------------------------

        // INDEX examtest_id
        $this->createIndex(
            'idx-examtest_question-examtest_id',
            'examtest_question',
            'examtest_id'
        );

        // FOREIGN KEY for examtest_id
        $this->addForeignKey(
            'fk-examtest_question-examtest_id',
            'examtest_question',
            'examtest_id',
            'examtest',
            'id',
            'CASCADE'
        );

        // ANSWERS
        // ------------------------------------------

        // INDEX question_id
        $this->createIndex(
            'idx-examtest_answer-question_id',
            'examtest_answer',
            'question_id'
        );

        // FOREIGN KEY question_id
        $this->addForeignKey(
            'fk-examtest_answer-question_id',
            'examtest_answer',
            'question_id',
            'examtest_question',
            'id',
            'CASCADE'
        );

        ///////-----------------------------------
        // END EXAMTEST

        //////////////////////////////////////////
        // EXAMWRITE
        ///////-----------------------------------

        // INDEX exercise_id
        $this->createIndex(
            'idx-examwrite-exercise_id',
            'examwrite',
            'exercise_id'
        );

        // FOREIGN KEY for exercise_id
        $this->addForeignKey(
            'fk-examwrite-exercise_id',
            'examwrite',
            'exercise_id',
            'examsection_exercise',
            'id',
            'CASCADE'
        );

        // --------------------

        // INDEX lesson_id
        $this->createIndex(
            'idx-examwrite-lesson_id',
            'examwrite',
            'lesson_id'
        );

        // FOREIGN KEY for lesson_id
        $this->addForeignKey(
            'fk-examwrite-lesson_id',
            'examwrite',
            'lesson_id',
            'lesson',
            'id',
            'CASCADE'
        );

        // --------------------

        // INDEX webinar_id
        $this->createIndex(
            'idx-examwrite-webinar_id',
            'examwrite',
            'webinar_id'
        );

        // FOREIGN KEY for webinar_id
        $this->addForeignKey(
            'fk-examwrite-webinar_id',
            'examwrite',
            'webinar_id',
            'webinar',
            'id',
            'CASCADE'
        );

        // ANSWERS
        // ------------------------------------------

        // INDEX examwrite_id
        $this->createIndex(
            'idx-examwrite_answer-examwrite_id',
            'examwrite_answer',
            'examwrite_id'
        );

        // FOREIGN KEY examwrite_id
        $this->addForeignKey(
            'fk-examwrite_answer-examwrite_id',
            'examwrite_answer',
            'examwrite_id',
            'examwrite',
            'id',
            'CASCADE'
        );

        // --------------------

        // INDEX user_id
        $this->createIndex(
            'idx-examwrite_answer-user_id',
            'examwrite_answer',
            'user_id'
        );

        // --------------------

        // INDEX teacher_id
        $this->createIndex(
            'idx-examwrite_answer-teacher_id',
            'examwrite_answer',
            'teacher_id'
        );

        ///////-----------------------------------
        // END EXAMWRITE


        //////////////////////////////////////////
        // EXAMCORRELATE
        ///////-----------------------------------

        // INDEX exercise_id
        $this->createIndex(
            'idx-examcorrelate-exercise_id',
            'examcorrelate',
            'exercise_id'
        );

        // FOREIGN KEY for exercise_id
        $this->addForeignKey(
            'fk-examcorrelate-exercise_id',
            'examcorrelate',
            'exercise_id',
            'examsection_exercise',
            'id',
            'CASCADE'
        );

        // PAIR
        // ------------------------------------------

        // INDEX examcorrelate_id
        $this->createIndex(
            'idx-examcorrelate_pair-examcorrelate_id',
            'examcorrelate_pair',
            'examcorrelate_id'
        );

        // FOREIGN KEY examcorrelate_id
        $this->addForeignKey(
            'fk-examcorrelate_pair-examcorrelate_id',
            'examcorrelate_pair',
            'examcorrelate_id',
            'examcorrelate',
            'id',
            'CASCADE'
        );

        ///////-----------------------------------
        // END EXAMCORRELATE


        //////////////////////////////////////////
        // EXAMADDITION
        ///////-----------------------------------

        // INDEX exercise_id
        $this->createIndex(
            'idx-examaddition-exercise_id',
            'examaddition',
            'exercise_id'
        );

        // FOREIGN KEY exercise_id
        $this->addForeignKey(
            'fk-examaddition-exercise_id',
            'examaddition',
            'exercise_id',
            'examsection_exercise',
            'id',
            'CASCADE'
        );

        ///////-----------------------------------
        // END EXAMADDITION


        //////////////////////////////////////////
        // FULLEXAM
        ///////-----------------------------------

        // INDEX course_id
        $this->createIndex(
            'idx-fullexam-course_id',
            'fullexam',
            'course_id'
        );

        // FOREIGN KEY for course_id
        $this->addForeignKey(
            'fk-fullexam-course_id',
            'fullexam',
            'course_id',
            'course',
            'id',
            'SET NULL'
        );

        // INDEX subject_id
        $this->createIndex(
            'idx-fullexam-subject_id',
            'fullexam',
            'subject_id'
        );

        // SECTION
        // ------------------------------------------

        // INDEX fullexam_id
        $this->createIndex(
            'idx-examsection-fullexam_id',
            'examsection',
            'fullexam_id'
        );

        // FOREIGN KEY for fullexam_id
        $this->addForeignKey(
            'fk-examsection-fullexam_id',
            'examsection',
            'fullexam_id',
            'fullexam',
            'id',
            'CASCADE'
        );

        // EXERCISE
        // ------------------------------------------

        // INDEX section_id
        $this->createIndex(
            'idx-examsection_exercise-section_id',
            'examsection_exercise',
            'section_id'
        );

        // FOREIGN KEY for section_id
        $this->addForeignKey(
            'fk-examsection_exercise-section_id',
            'examsection_exercise',
            'section_id',
            'examsection',
            'id',
            'CASCADE'
        );

        // RESULT
        // ------------------------------------------

        // INDEX fullexam_id
        $this->createIndex(
            'idx-result_fullexam-fullexam_id',
            'result_fullexam',
            'fullexam_id'
        );

        // FOREIGN KEY for fullexam_id
        $this->addForeignKey(
            'fk-result_fullexam-fullexam_id',
            'result_fullexam',
            'fullexam_id',
            'fullexam',
            'id',
            'CASCADE'
        );

        // --------------------

        // INDEX user_id
        $this->createIndex(
            'idx-result_fullexam-user_id',
            'result_fullexam',
            'user_id'
        );

        // --------------------

        // INDEX teacher_id
        $this->createIndex(
            'idx-result_fullexam-teacher_id',
            'result_fullexam',
            'teacher_id'
        );

        ///////-----------------------------------
        // END FULLEXAM
    }

    public function safeDown()
    {
        // FULLEXAM
        // ---------------------------------------------------

        // RESULT
        $this->dropIndex('idx-result_fullexam-teacher_id', 'result_fullexam');              // TEACHER_ID
        $this->dropIndex('idx-result_fullexam-user_id', 'result_fullexam');                 // USER_ID
        $this->dropForeignKey('fk-result_fullexam-fullexam_id', 'result_fullexam');         // FULLEXAM_ID
        $this->dropIndex('idx-result_fullexam-fullexam_id', 'result_fullexam');

        // EXERCISE
        $this->dropForeignKey('fk-examsection_exercise-section_id', 'examsection_exercise'); // SECTION_ID
        $this->dropIndex('idx-examsection_exercise-section_id', 'examsection_exercise');

        // SECTION
        $this->dropForeignKey('fk-examsection-fullexam_id', 'examsection');                 // FULLEXAM_ID
        $this->dropIndex('idx-examsection-fullexam_id', 'examsection');

        // MAIN
        $this->dropIndex('idx-fullexam-subject_id', 'fullexam');                            // SUBJECT_ID
        $this->dropForeignKey('fk-fullexam-course_id', 'fullexam');                         // COURSE_ID
        $this->dropIndex('idx-fullexam-course_id', 'fullexam');

        ////// END FULLEXAM

        ///////////////////////////////////////////////

        // EXAMADDITION
        // ---------------------------------------------------

        $this->dropForeignKey('fk-examaddition-exercise_id', 'examaddition');               // EXERCISE_ID
        $this->dropIndex('idx-examaddition-exercise_id', 'examaddition');

        ////// END EXAMADDITION

        ///////////////////////////////////////////////

        // EXAMCORRELATE
        // ---------------------------------------------------

        // PAIR
        $this->dropForeignKey('fk-examcorrelate_pair-examcorrelate_id',
            'examcorrelate_pair');  // EXAMCORRELATE_ID
        $this->dropIndex('idx-examcorrelate_pair-examcorrelate_id', 'examcorrelate_pair');

        // MAIN
        $this->dropForeignKey('fk-examcorrelate-exercise_id',
            'examcorrelate');       // EXERCISE_ID
        $this->dropIndex('idx-examcorrelate-exercise_id', 'examcorrelate');

        ////// END EXAMCORRELATE

        ///////////////////////////////////////////////

        // EXAMWRITE
        // ---------------------------------------------------

        // ANSWERS
        $this->dropIndex('idx-examwrite_answer-teacher_id', 'examwrite_answer');            // TEACHER_ID
        $this->dropIndex('idx-examwrite_answer-user_id', 'examwrite_answer');               // USER_ID
        $this->dropForeignKey('fk-examwrite_answer-examwrite_id', 'examwrite_answer');      // EXAMWRITE_ID
        $this->dropIndex('idx-examwrite_answer-examwrite_id', 'examwrite_answer');

        // MAIN
        $this->dropForeignKey('fk-examwrite-webinar_id', 'examwrite');                      // WEBINAR_ID
        $this->dropIndex('idx-examwrite-webinar_id', 'examwrite');
        $this->dropForeignKey('fk-examwrite-lesson_id', 'examwrite');                       // LESSON_ID
        $this->dropIndex('idx-examwrite-lesson_id', 'examwrite');
        $this->dropForeignKey('fk-examwrite-exercise_id', 'examwrite');                     // EXERCISE_ID
        $this->dropIndex('idx-examwrite-exercise_id', 'examwrite');

        ////// END EXAMWRITE

        ///////////////////////////////////////////////

        // EXAMTEST
        // ---------------------------------------------------

        // ANSWERS
        $this->dropForeignKey('fk-examtest_answer-question_id', 'examtest_answer');         // QUESTION_ID
        $this->dropIndex('idx-examtest_answer-question_id', 'examtest_answer');

        // QUESTIONS
        $this->dropForeignKey('fk-examtest_question-examtest_id', 'examtest_question');     // EXAMTEST_ID
        $this->dropIndex('idx-examtest_question-examtest_id', 'examtest_question');

        // MAIN
        $this->dropForeignKey('fk-examtest-webinar_id', 'examtest');                        // WEBINAR_ID
        $this->dropIndex('idx-examtest-webinar_id', 'examtest');
        $this->dropForeignKey('fk-examtest-lesson_id', 'examtest');                         // LESSON_ID
        $this->dropIndex('idx-examtest-lesson_id', 'examtest');
        $this->dropForeignKey('fk-examtest-exercise_id', 'examtest');                       // EXERCISE_ID
        $this->dropIndex('idx-examtest-exercise_id', 'examtest');

        ////// END EXAMTEST
    }
}
