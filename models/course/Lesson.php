<?php

namespace app\models\course;

use Yii;
use app\models\exam\test\Test;
use app\models\exam\write\Write;

/**
 * This is the model class for table "lesson".
 *
 * @property int $id
 * @property int $module_id
 * @property int $examtest_id
 * @property int $examwrite_id
 * @property string $video
 * @property string $title
 * @property string $desc
 * @property int $place
 * @property int $free
 * @property int $publish
 * @property int $for_access
 *
 * @property Examprogress[] $examprogresses
 * @property Test $test
 * @property Write $write
 * @property Module $module
 * @property Progress[] $progresses
 */
class Lesson extends \yii\db\ActiveRecord
{
    public $course_id;

    public static function tableName()
    {
        return 'lesson';
    }

    public function rules()
    {
        return [
            [['module_id', 'video', 'title'], 'required'],
            [['module_id', 'examtest_id', 'examwrite_id', 'place', 'free', 'publish'], 'integer'],
            [['desc', 'links'], 'string'],
            [['video', 'title'], 'string', 'max' => 255],
            [['examtest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['examtest_id' => 'id']],
            [['examwrite_id'], 'exist', 'skipOnError' => true, 'targetClass' => Write::className(), 'targetAttribute' => ['examwrite_id' => 'id']],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::className(), 'targetAttribute' => ['module_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'video' => 'Видео',
            'title' => 'Заголовок',
            'desc' => 'Описание',
            'place' => 'Место',
            'free' => 'Бесплатный',
            'publish' => 'Опубликованный',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamprogresses()
    {
        return $this->hasMany(Examprogress::className(), ['lesson_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Examtest::className(), ['id' => 'examtest_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWrite()
    {
        return $this->hasOne(Examwrite::className(), ['id' => 'examwrite_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgresses()
    {
        return $this->hasMany(Progress::className(), ['lesson_id' => 'id']);
    }
}
