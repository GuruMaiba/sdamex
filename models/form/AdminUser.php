<?php

namespace app\models\form;

use Yii;
use yii\rbac\Role;
use yii\behaviors\TimestampBehavior;
use app\models\{User, Teacher};
use app\models\promoter\Code;
use app\components\UserStatus;
use yii\imagine\Image;

class AdminUser extends AppModelForm
{
    public $id;
    public $ava                 = User::DEF_AVA;
    public $coords              = [];
    public $notCrop             = false;
    public $username;
    public $email;
    public $password;
    public $phone;
    public $name;
    public $surname;
    public $phrase;
    public $skype;
    public $teacher_class       = 0;
    public $cash                = 0;
    public $seller_id;

    private $savePath           = 'css/images/users/';
    public $image               = null;
    public $_user               = null;
    public $roles               = [];
    public $role;
    public $teacher_option;
    public $promoter_codes;
    public $subjects            = '';
    public $subjectsArr;
    public $specialization      = '';
    public $specializationArr;

    public function rules()
    {
        return [
            [['id', 'teacher_class', 'seller_id'], 'integer'],
            [['username', 'email'], 'required'],
            [['username', 'email'], 'trim'],
            ['email', 'email'],
            ['email', 'unique',
                'filter' => ['!=', 'id', $this->id],
                'targetClass' => 'app\models\User',
                'message' => 'Этот E-mail адрес уже зарегистрирован!'],
            ['username', 'unique',
                'filter' => ['!=', 'id', $this->id],
                'targetClass' => 'app\models\User',
                'message' => 'Этот Nickname занят!'],

            [['cash'], 'number'],
            [['image'], 'file', 'extensions' => 'png, jpg', 'maxSize' => 20*1024*1024], // 20MB
            [['phrase', 'password'], 'string'],
            [['phone'], 'string', 'max' => 18],
            [['ava', 'name', 'surname', 'skype', 'role'], 'string', 'max' => 255],

            ['ava', 'default', 'value' => User::DEF_AVA],
            
            [['seller_id'], 'exist', 'targetClass' => User::className(), 'targetAttribute' => ['seller_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'seller_id' => 'ID Вербовщика',
            'image' => 'Аватарка',
            'username' => 'Nickname',
            'email' => 'E-mail',
            'password' => 'Пароль',
            'phone' => 'Телефон',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'phrase' => 'То, чем пользователь хочет поделиться',
            'skype' => 'Логин в skype',
            'cash' => 'Баланс',
            'teacher_class' => 'Количество занятий с учителем',
            'role' => 'Роль пользователя',
        ];
    }

    // Находим пользователя и передаём его параметры в текущую модель
    public function getValue($id) {
        $user = $this->findUser($id);
        if (!$user)
            return false;

        $attr = [
            'id' => $user->id,
            'ava' => $user->ava,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'name' => $user->name,
            'surname' => $user->surname,
            'phrase' => $user->phrase,
            'skype' => $user->skype,
            'cash' => $user->cash,
            'teacher_class' => $user->teacher_class,
        ];
        $this->attributes = $attr;

        $role = current(Yii::$app->authManager->getRolesByUser($user->id));
        $this->role = $role->name;
        if ($role->name == 'teacher' || $role->name == 'mainTeacher')
            $this->teacher_option = $user->teacher;
        if ($role->name != 'user')
            $this->promoter_codes = Code::find()->where(['promoter_id'=>$user->id])->asArray()->all();
        if ($role->name == 'promoter' && $user->seller != null)
            $this->seller_id = $user->seller->id;
        
        $this->subjects = [];
        $this->subjectsArr = Yii::$app->params['listSubs'];
        foreach ($this->subjectsArr as $sId => $sub) {
            if ($sub['isActive']) {
                $this->subjects[$sId] = [
                    'isActive' => (strpos($user->teacher->subjects, "$sId") !== false),
                    'name' => $sub['name'],
                ];
            }
        }

        $this->specialization = [];
        $this->specializationArr = Teacher::getSpec(Yii::$app->params['subInx']);
        foreach ($this->specializationArr as $key => $val)
            $this->specialization[$val] = (strpos($user->teacher->specialization, $val) === false) ? '' : 'active';

        return true;
    }

    public function changeUserOption() {
        $user = $this->findUser($this->id);

        if ($user->image != null) {
            // Работа с изображением
            // $user->image = $model->image;
            $model->image = $user->ava;
            $user->ava = $user->avaUpload($this->coords, $this->notCrop);
            if ($model->image != 'no_img.jpg' && $model->image != 'del.jpg') {
                unlink(Yii::getAlias("@uAvaLarge/$model->image"));
                unlink(Yii::getAlias("@uAvaSmall/$model->image"));
            }
        }

        $user->username = $this->username;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->name = $this->name;
        $user->surname = $this->surname;
        $user->phrase = $this->phrase;
        $user->skype = $this->skype;
        $user->cash = $this->cash;
        $user->teacher_class = $this->teacher_class;
        $user->statistics = json_encode([ Yii::$app->params['subInx'] => [] ]);
        $user->setPassword($this->password);
        $user->generateAuthKey();

        if ($user->id == 0)
            $user->status = UserStatus::ACTIVE;
        if (!empty($this->seller_id) && ($user->seller == null || $user->seller->id != $this->seller_id)) {
            $seller = User::find()->where(['id'=>$this->seller_id])->limit(1)->one();
            if ($seller)
                $user->link('seller', $seller);
        }
        $user->save();

        if ($user->role->name != $this->role) {
            if ($user->role->name == 'teacher' || $user->role->name == 'mainTeacher') {
                // Находим учителя
                $teacher = Teacher::find()->where(['user_id' => $user->id])->limit(1)->one();
                // удаляем строку
                if ($teacher)
                    $teacher->delete();
            }
        }

        $user->setRole($this->role);
        if ($this->role == 'teacher' || $this->role == 'mainTeacher')
            $this->teacher_option = Teacher::find()->where(['user_id' => $user->id])->limit(1)->one();

        return true;
    }

    // Загрузить картинку
    public function upload($coords) {
        $this->savePath = Yii::getAlias("@uAvaSmall");
        $name = $this->image->baseName . '.' . $this->image->extension;
        $path = Yii::$app->params['teampsPath'].$name;

        $this->image->saveAs($path);

        do {
            $name = substr(str_shuffle($this->permitted_chars), 0, 16).'.'.$this->image->extension;
        } while (file_exists($this->savePath.$name));

        Image::crop($path, $coords['W'], $coords['H'], [$coords['X'], $coords['Y']])
            ->save($this->savePath.$name, ['quality' => 70]);
        unlink($path);

        $this->deleteAva();

        $this->image = null;
        $this->ava = $name;
    }

    public function deleteAva() {
        if ($this->ava != User::DEF_AVA && file_exists($this->savePath.$this->ava)) {
            unlink($this->savePath.$this->ava);
            return true;
        } else { return false; }
    }

    public function findUser($id=0) {
        if ($this->_user)
            return $this->_user;
        else if ($id > 0)
            return $this->_user = User::findOne($id);
        else
            return $this->_user = new User;
    }
} // end User
