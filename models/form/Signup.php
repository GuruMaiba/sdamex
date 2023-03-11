<?php

namespace app\models\form;

use Yii;
use yii\base\Model;
use app\models\User;
use app\components\UserStatus;

class Signup extends Model
{
    public $username;
    public $email;
    public $password;
    public $retypePassword;
    public $domain;
    public $inviteCode;

    public function rules()
    {
        return [
            [['email', 'password', 'retypePassword'], 'required'],
            [['email', 'username'], 'filter', 'filter' => 'trim'],

            ['username', 'validUsername'],

            ['email', 'email'],
            ['email', 'unique', 'targetClass' => 'app\models\User', 'message' => 'Этот E-mail адрес уже зарегистрирован!'],

            ['password', 'string', 'min' => 6, 'max' => 50],
            ['inviteCode', 'string', 'min' => 3, 'max' => 20],
            ['retypePassword', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    public function validUsername($attribute, $params) {
        if (!$this->hasErrors())
            $this->username = User::validateUsername($this->username);
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup($sub=0)
    {
        $split = explode('@',$this->email);
        $this->username = $split[0];
        $this->domain = $split[1];

        if ($this->validate()) {
            $mainInx = Yii::$app->params['subInx'];
            $stats = [ $mainInx => [] ];
            if ($sub > $mainInx)
                $stats[$sub] = User::DEF_STAT; 

            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = UserStatus::INACTIVE;
            $user->invite_code = $this->inviteCode;
            $user->important_updated_at = time();
            $user->statistics = json_encode($stats);
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->generateToken();
            $user->save();

            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole('user');
            $auth->assign($authorRole, $user->getId());

            return $user;
        }

        return null;
    }
}
