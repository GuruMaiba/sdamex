<?php

namespace app\models\form;

use Yii;
use yii\rbac\Role;
use yii\base\Model;
use yii\helpers\Url;
// use yii\behaviors\TimestampBehavior;
use app\models\User;
use app\models\course\{Course, Student};
use app\models\webinar\{Webinar, Member};
use app\models\promoter\Code;
use app\components\{UserStatus, CodeType};
use yii\imagine\Image;

class UserSettings extends Model
{
    public $id;
    public $username;
    public $email;
    public $phone;
    public $first_name;
    public $last_name;
    public $old_pass;
    public $new_pass;
    public $retype_pass;
    public $invite_code;

    private $savePath       = 'web/css/images/users/';
    public $code            = null;
    public $image           = null;
    public $_user           = null;
    // public $teacher_option;

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image' => 'Аватарка',
            'username' => 'Nickname',
            'email' => 'E-mail',
            'phone' => 'Телефон',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'old_pass' => 'Текущий пароль',
            'new_pass' => 'Новый пароль',
            'retype_pass' => 'Повтор пароля',
            'invite_code' => 'Инвайт-код',
        ];
    }

    public function rules()
    {
        return [
            [['id'], 'integer'],

            [['username', 'email'], 'required', 'message' => 'Nickname и E-mail обязательны для заполнения'],
            [['username', 'email'], 'trim'],

            ['email', 'email', 'message' => 'Введите корректный E-mail адрес'],
            ['email', 'unique',
                'filter' => ['!=', 'id', $this->id],
                'targetClass' => 'app\models\User',
                'message' => 'Этот E-mail адрес уже зарегистрирован!'],
                
            [['username'], 'string', 'min' => 3, 'max' => 20],
            ['username', 'match', 'pattern' => '/^[a-zA-Z]\w*$/i', 'message' => 'Nickname должен начинаться с буквы и состоять из букв или цифр'],
            ['username', 'unique',
                'filter' => ['!=', 'id', $this->id],
                'targetClass' => 'app\models\User',
                'message' => 'Этот Nickname занят!'],

            [['phone'], 'string', 'max' => 18],
            [['first_name', 'last_name'], 'string', 'max' => 255],

            ['old_pass', function ($attr, $params, $validator) {
                $user = $this->getUser($this->id);
                if (!$user->validatePassword($this->$attr))
                    $this->addError($attr, 'Текущий пароль введён неверно');
            }],
            [['old_pass', 'new_pass'], 'string', 'min' => 6, 'max' => 50],
            ['retype_pass', 'compare', 'compareAttribute' => 'new_pass', 'message' => 'Повторный пароль не совпадает с новым паролем'],
            // ['new_pass', 'match',
            //     'pattern' => '/^[a-zA-Z0-9!@#$%^&*()-_=+{};:,<.>]{6,50}$/',
            //     'message' => 'Длина пароля 6-50 символов, он должен состоять из латинских букв разных регистров, включать цифры и использовать 1 спец.символ - !@#$%^&*()-_=+{};:,<.>'],
            [['invite_code'], 'string', 'min' => 4, 'max' => 20],
        ];
    }

    public function changeUserOption() {
        $user = $this->getUser($this->id);
        if ($user == null) {
            $this->addError('model', 'Что-то пошло не так!');
            return false;
        }

        $user->username = $this->username;
        if ($user->role->name != 'promoter' && $user->role->name != 'seller') {
            $user->phone = $this->phone;
            $user->name = strip_tags($this->first_name);
            $user->surname = strip_tags($this->last_name);
        }

        if (empty($user->invite_code) && !empty($this->invite_code)) {
            $code = Code::find()->where(['code'=>$this->invite_code])->asArray()->limit(1)->one();

            if (!$code) {
                $this->addError('invite_code', 'Инвайт-код не найден!');
                return false;
            } else if ($code['end_at'] < time()) {
                $this->addError('invite_code', 'Инвайт-код просрочен!');
                return false;
            }

            $this->code = $code;
            $user->invite_code = $this->invite_code;

            $free = CodeType::getPropsArr();
            $free = $free[$code['type']]['free_access'];
            if (!empty($free)) {
                $user->teacher_class += (int)$free['lessons'];

                if ($free['courses'] != []) {
                    $courses = Course::find()->select(['id'])
                        ->where(['in', 'id', $free['courses']])->asArray()->all();
                    foreach ($courses as $crs) {
                        $student = new Student;
                        $student->learner_id = $user->id;
                        $student->course_id = $crs['id'];
                        $student->start_at = time();
                        $student->end_at = time() + (7*24*3600);
                        $student->save();
                    }
                }

                if ($free['webinars'] != []) {
                    $webinars = Webinar::find()->select(['id'])
                        ->where(['in', 'id', $free['webinars']])->asArray()->all();
                    foreach ($webinars as $web) {
                        $member = new Member;
                        $member->user_id = $user->id;
                        $member->webinar_id = $web['id'];
                        $member->save();
                    }
                }
            } // end if $free
        }

        // DEBUG: Добавить проверку важных обновлений, чтобы не частили
        if ($this->new_pass) {
            if ($user->password_hash) {
                if ($this->old_pass) {
                    $user->setPassword($this->new_pass);
                } else {
                    $this->addError('old_pass', 'Введите текущий пароль!');
                    return false;
                }
            } else
                $user->setPassword($this->new_pass);
        }

        if ($user->email != $this->email) {
            if ($user->important_updated_at + (24*3600) < time()) {
                $user->new_email = $this->email;
                $user->important_updated_at = time();
                $user->generateToken();

                $url = Url::toRoute([
                    'account/confirm',
                    'id' => $user->id,
                    'token' => $user->token
                ], true);

                // send mail
                $mail = Yii::$app->mailer->compose('confirmEmail', ['url'=>$url]) // result rendering view
                    ->setFrom([ Yii::$app->params['mailingEmail'] => Yii::$app->params['shortName'] ])
                    ->setTo($user->new_email)
                    ->setSubject('Подтверждение новой почты')
                    ->send();
            } else {
                $this->addError('email', 'Вы недавно произведили Важные изменения, попробуйте повторить в другое время!');
                return false;
            }
        }

        $user->update();

        return true;
    }

    public function getUser($id) {
        if ($this->_user == null)
            $this->_user = User::find()->where(['id'=>$id])->limit(1)->one();

        return $this->_user;
    }
} // end Settings
