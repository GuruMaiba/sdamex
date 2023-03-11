<?php

namespace app\models\course;

use Yii;
use app\models\webinar\Webinar;

/**
 * This is the model class for table "course".
 *
 * @property int $id
 * @property string $title
 * @property string $desc
 * @property int $free
 * @property int $publish
 * @property decimal $cost (19,0)
 *
 * @property Module[] $modules
 * @property Progress[] $progresses
 */
class Course extends \app\models\AppActiveRecord
{
    public $subjects = [];

    public static function tableName()
    {
        return 'course';
    }

    public function rules()
    {
        return [
            [['title', 'subject_id'], 'required'],
            [['desc', 'author_desc', 'ava'], 'string'],
            [['cost'], 'number'],
            [['image'], 'file', 'extensions' => 'png, jpg'],
            [['subject_id', 'author_id'], 'integer'],
            [['free', 'publish'], 'boolean'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'author_id' => 'ID Автора',
            'subject_id' => 'Предмет',
            'image' => 'Обложка',
            'ava' => 'Путь к картинке',
            'title' => 'Название',
            'desc' => 'Описание',
            'author_desc' => 'Описание автора',
            'cost' => 'Стоимость',
            'free' => 'Бесплатный',
            'publish' => 'Опубликованный',
        ];
    }

    public function getListCourses($asArr = true, $all = false, $free = false, $pub = true)
    {
        $query = Course::find();
        if (!$all)
            $query = $query->where(['free'=>$free, 'publish'=>$pub]);
        if ($asArr)
            $query = $query->asArray();

        return $query->all();
    }

    public function getModules()
    {
        return $this->hasMany(Module::className(), ['course_id' => 'id'])->asArray();
    }

    public function getProgresses()
    {
        return $this->hasMany(ProgressCourse::className(), ['course_id' => 'id'])->asArray();
    }

    public function checkProgressByUserId($user_id)
    {
        return $this->hasOne(ProgressCourse::className(), ['course_id' => 'id'])
            ->andWhere(['learner_id'=>$user_id])
            ->exists();
    }

    public function getWebinars()
    {
        return $this->hasMany(Webinar::className(), ['id' => 'webinar_id'])
            ->viaTable('course_webinar', ['course_id' => 'id']);
    }
}
