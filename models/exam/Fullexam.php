<?php

namespace app\models\exam;

use Yii;

/**
 * This is the model class for table "fullexam".
 *
 * @property int $id
 * @property string $name
 * @property string $desc
 * @property int $publish
 *
 * @property Section[] $sections
 */
class Fullexam extends \yii\db\ActiveRecord
{
    public $courses = [];
    public $subjects = [];
    public const MAX_TRY_PREM = 5;
    public const MAX_TRY_FREE = 1;

    public static function tableName()
    {
        return 'fullexam';
    }

    public function rules()
    {
        return [
            [['subject_id', 'name'], 'required'],
            [['course_id', 'subject_id', 'max_points', 'timer'], 'integer'],
            [['desc'], 'string'],
            [['publish'], 'boolean'],
            [['name', 'marks'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'course_id' => 'Курсы',
            'subject_id' => 'Предметы',
            'name' => 'Название экзамена',
            'desc' => 'Описание',
            'publish' => 'Опубликовать',
            'max_exp' => 'Максимальное число опыта за экзамен',
            'max_points' => 'Максимальное число балов за экзамен',
            'marks' => 'Оценка - диапазон очков',
        ];
    }

    public function getSections()
    {
        return $this->hasMany(Section::className(), ['fullexam_id' => 'id'])
            ->orderBy('place');
    }
}
