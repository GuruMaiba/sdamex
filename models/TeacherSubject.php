<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "teacher_subject".
 *
 * @property int $teacher_id
 * @property int $subject_id
 */
class TeacherSubject extends \app\models\AppActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_subject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_id', 'subject_id'], 'required'],
            [['teacher_id', 'subject_id'], 'integer'],
            [['teacher_id', 'subject_id'], 'unique', 'targetAttribute' => ['teacher_id', 'subject_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'teacher_id' => 'Teacher ID',
            'subject_id' => 'Subject ID',
        ];
    }
}
