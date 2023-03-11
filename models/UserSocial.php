<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_social".
 *
 * @property string $social_id
 * @property int $user_id
 *
 * @property User $user
 */
class UserSocial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_social';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['social_id', 'user_id', 'type'], 'required'],
            [['social_id'], 'unique'],
            [['social_id'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 2],
            [['user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'social_id' => 'Social ID',
            'user_id' => 'User ID',
            'type' => 'Type'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
