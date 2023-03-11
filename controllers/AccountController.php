<?php

namespace app\controllers;

use Yii;

use yii\helpers\Url;
use yii\imagine\Image;
    use Imagine\Image\Box;
use yii\httpclient\Client;
use yii\web\{Response, UploadedFile};

use app\models\{User, UserSocial, BanUser};
use app\models\form\{Signin, Signup, NewPass, UserSettings};
use app\models\course\{Course, Student};
use app\models\webinar\{Webinar, Member};
use app\models\promoter\Code;
use app\components\{UserStatus, CodeType};
// use vintage\tinify\UploadedFile;

class AccountController extends AppController
{
    public $layout = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'login'             => ['GET'],
                    'logout'            => ['GET'],
                    'signin'            => ['POST'],
                    'signup'            => ['POST'],
                    'resetpass'         => ['POST'],
                    'newpass'           => ['GET'],
                    'set-newpass'       => ['POST'],
                    'change-option'     => ['POST'],
                    'sub-change-ava'    => ['POST'],
                    'oauth-vk'          => ['GET'],
                    'oauth-fb'          => ['GET'],
                    'oauth-gl'          => ['GET'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['sub-change-ava', 'change-option'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['sub-change-ava', 'change-option'],
                        'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->request->post('secretKey') === Yii::$app->params['secretKey']);
                        },
                    ],
                ],
            ],
        ];
    }

    // VKontakte
    // ------------------------------------------------------------------------
    protected const CLIENT_ID_VK = '7527305';
    protected const CLIENT_SECRET_VK = '1I8JrLB0xBOKkvWh0Jre';

    // Facebook
    // ------------------------------------------------------------------------
    protected const CLIENT_ID_FB = '301248721244030';
    protected const CLIENT_SECRET_FB = '827d54dbf5f7c1393c7b898e780b3ba0';

    // Google
    // ------------------------------------------------------------------------
    protected const CLIENT_ID_GL = '281510484117-lloo1qm2tvr9ssb405tortp4ulagah1j.apps.googleusercontent.com';
    protected const CLIENT_SECRET_GL = '0SxKYkcLwKJmFAW6oxvqpW90';

    public function beforeAction($action)
    {
        if (in_array($action->id, ['sub-change-ava', 'change-option'])) { //'signin', 'signup', 
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Login
     * @return string
     */
    public function actionLogin()
    {
        $sub = Yii::$app->params['listSubs'][$_GET['sub']];
        $sub['id'] = $_GET['sub'];
        $redir = ($sub && $sub['isActive']) ? $sub['link'] : ['/'];
        
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            // из-за междоменной куки, yii2 моросит на сервере, затираем в ручную
            unset($_COOKIE['PHPSESSID']); 
            setcookie('PHPSESSID', null, -1, '/', '.sdamex.ru');
            if (YII_ENV_DEV)
                setcookie('PHPSESSID', null, -1, '/', '.sdamex.loc');
        }

        if ($sub && $sub['isActive']
            && $sub['id'] > Yii::$app->params['subInx']
            && (empty($_COOKIE['authSub']) || $sub['id'] != $_COOKIE['authSub']))
            setcookie('authSub', $sub['id'], time()+3600, '/');

        $this->view->title = 'Авторизация, вход, регистрация | '.Yii::$app->params['shortName'];
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => 'Авторизуйся, чтобы использовать весь возможный функционал сайта: тестирования, статистика, бесплатные вебинары, общение, интересные посты и многое другое, присоединяйся!',
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => 'Авторизация, вход, регистрация, '.Yii::$app->params['commonKeyWords'],
        ]);

        return $this->render('login', [
            'page'=> ($_GET['p'] != 'signin' && $_GET['p'] != 'signup') ? 'signin' : $_GET['p']
        ]);
    }

    public function actionSignin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Signin();
        $request = [ 'type' => 'error', 'messages' => ['Что-то пошло не так!'] ];

        if ($model->load($_POST)) {
            $request['messages'] = [];
            $model->rememberMe = ($model->rememberMe == 'on') ? true : false;
            if ($model->validate()) {
                if ($model->validateUser() && $model->login($_COOKIE['authSub'])) {
                    $request['type'] = 'success';
                    $request['messages']['link'] = Url::to('/');

                    $sub = $_COOKIE['authSub'];
                    if ($sub && $sub != Yii::$app->params['subInx']) {
                        $sub = Yii::$app->params['listSubs'][$sub];
                        if ($sub && $sub['isActive'])
                            $request['messages']['link'] = $sub['link'].'personal/profile';
                        else
                            $sub = null;
                    }

                    if (empty($sub)) {
                        foreach (Yii::$app->params['listSubs'] as $id => $subject) {
                            if ($id != Yii::$app->params['subInx'] && $subject['isActive'])
                                $sub = $subject;
                        }
                        if (!empty($sub))
                            $request['messages']['link'] = $sub['link'].'personal/profile';
                    }
                } else
                    $request = $this->errorList($request, $model->getErrors('allErrors'));
            } else {
                $request = $this->errorList($request, $model->getErrors('email'));
                $request = $this->errorList($request, $model->getErrors('password'));
                $request = $this->errorList($request, $model->getErrors('allErrors'));
            }
        } // end load

        // отправляем json
        return $request;
    }

    /**
     * Logout
     * @return Response
     */
    public function actionLogout($sub=1)
    {
        Yii::$app->user->logout();
        // из-за междоменной куки, yii2 моросит на сервере, затираем в ручную
        unset($_COOKIE['PHPSESSID']); 
        setcookie('PHPSESSID', null, -1, '/', '.sdamex.ru');
        if (YII_ENV_DEV)
            setcookie('PHPSESSID', null, -1, '/', '.sdamex.loc');
        return $this->redirect(['/account/login', 'sub'=>$sub]);
    }

    public function actionSignup()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Signup();
        $request = [ 'type' => 'error', 'messages' => ['Что-то пошло не так! Данные не загружены в модель!'] ];

        if ($model->load($_POST)) {
            $code = null;
            $request['messages'] = [];

            if (!empty($model->inviteCode)) {
                $code = Code::find()->where(['code'=>$model->inviteCode])->asArray()->limit(1)->one();
                if ($code) {
                    if ($code['end_at'] < time()) {
                        $request['messages'][] = 'Промокод просрочен!';
                        return $request;
                    }
                } else {
                    $request['messages'][] = 'Промокод не найден!';
                    return $request;
                }
            }

            if ($user = $model->signup($_COOKIE['authSub'])) {
                // Settings back url https:/website/account/confirm?id=..&token=..&subject=..
                $url = Url::toRoute([
                    'account/confirm',
                    'id' => $user->id,
                    'token' => $user->token
                ], true);

                // send mail
                $mail = Yii::$app->mailer->compose('confirmEmail', ['url'=>$url]) // result rendering view
                    ->setFrom([ Yii::$app->params['mailingEmail'] => Yii::$app->params['shortName'] ])
                    ->setTo($user->email)
                    ->setSubject('Подтверждение почты')
                    ->send();

                if ($code) {
                    $free = CodeType::getPropsArr();
                    $free = $free[$code['type']]['free_access'];
                    if (!empty($free)) {
                        $user->teacher_class += (int)$free['lessons'];
                        $user->update();

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
                } // end if $code

                $request['type'] = 'success';
                $request['messages'] = $model->domain;
            } else {
                $request = $this->errorList($request, $model->getErrors('email'));
                $request = $this->errorList($request, $model->getErrors('password'));
                $request = $this->errorList($request, $model->getErrors('retypePassword'));
            }
        }

        return $request;
    }

    public function actionMailAgain()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $email = $_POST['email'];
        $request = [ 'type' => 'error', 'messages' => ['Что-то пошло не так!'] ];
        if (empty($email)) {
            $request['messages'] = ['Введите Email!'];
            return $request;
        }

        $user = User::findByEmail($email);
        if (!$user) {
            $request['messages'] = ['Пользователь с такой почтой не найден!'];
            return $request;
        }

        $now = time();
        if ($user->important_updated_at > $now) {
            $request['messages'] = ['Следующее письмо можно будет отправить<br>через '.ceil(($user->important_updated_at - $now) / 60).' мин.'];
            return $request;
        }

        $user->important_updated_at = $now+(2*60);
        $user->generateToken();
        $user->update();

        $url = Url::toRoute([
            'account/confirm',
            'id' => $user->id,
            'token' => $user->token
        ], true);

        // send mail
        $mail = Yii::$app->mailer->compose('confirmEmail', ['url'=>$url]) // result rendering view
            ->setFrom([ Yii::$app->params['mailingEmail'] => Yii::$app->params['shortName'] ])
            ->setTo($user->email)
            ->setSubject('Подтверждение почты')
            ->send();
        
        $split = explode('@',$user->email);
        $request['type'] = 'success';
        $request['messages'] = $split[1];

        return $request;
    }

    public function actionConfirm($id, $token)
    {

        if (!Yii::$app->user->isGuest)
            Yii::$app->user->logout();

        $err = [ 'page' => 'signin', 'error' => 'true', 'errMessages' => ['Токен не действителен!'] ];
        $user = User::findByToken($id, $token);

        if ($user) {
            if (!empty($user->new_email)) {
                $user->email = $user->new_email;
                $user->new_email = null;
            }
            $user->status = UserStatus::ACTIVE;
            $user->token = null;

            // login(rememberMe = true)
            if ($user->update() && $user->login(true)) {
                $link = Yii::$app->params['listSubs'][$_COOKIE['authSub']]['link'];
                if ($link)
                    return $this->redirect($link.'personal/profile');
                return $this->goHome();
            } else
                $err['errMessages'] = ['Ошибка авторизации, обратитесь в службу поддержки.'];
        }

        return $this->render('login', $err);
    }

    public function actionResetpass()
    {
        $request = [ 'type' => 'error', 'messages' => ['Что-то пошло не так!'] ];
        if (isset($_POST['email'])) {
            $request['messages'] = [];
            $user = User::findByEmail($_POST['email']);
            if ($user) {
                $user->generateToken();

                // Settings back url http:/website/account/newpass?id=..&token=..
                $url = Url::toRoute([
                    'account/newpass',
                    'id' => $user->id,
                    'token' => $user->token
                ], true);

                // send mail
                $mail = Yii::$app->mailer->compose('newPass', ['url'=>$url]) // result rendering view
                    ->setFrom([ Yii::$app->params['mailingEmail'] => Yii::$app->params['shortName'] ])
                    ->setTo($user->email)
                    ->setSubject('Сброс пароля')
                    ->send();

                $user->update();

                $split = explode('@',$user->email);
                $request['type'] = 'success';
                $request['messages'] = $split[1];
            } else
                $request['messages'][] = 'Пользователь с такой почтой не найден.';
        }
        return json_encode($request);
    }

    public function actionNewpass($id, $token)
    {
        if (!Yii::$app->user->isGuest)
            return $this->redirect(['/']);
            
        $this->view->title = 'Изменение пароля | '.Yii::$app->params['shortName'];

        $vars = [
            'page' => 'signin',
            'id' => $id,
            'token' => $token,
            'error' => true,
            'errMessages' => ['Токен не действителен!']
        ];

        $user = User::findByToken($id, $token);
        if (!$user)
            return $this->render('login', $vars);

        $vars['page'] = 'newPass';
        $vars['error'] = false;
        $vars['errMessages'] = [];

        return $this->render('login', $vars);
    }

    public function actionSetNewpass()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = [ 'type' => 'error', 'messages' => ['Токен не действителен!'] ];
        $user = User::findByToken($_POST['id'], $_POST['token']);
        if ($user) {
            $pass = $_POST['newPass'];
            $ln = strlen($pass);
            $request['messages'] = [];

            if ($ln < 6 || $ln > 51)
                $request['messages'][] = 'Длина пароля должна составлять от 6 до 50 символов!';
            else if ($pass != $_POST['retypePass'])
                $request['messages'][] = 'Пароли не совпадают!';

            // $r0 = "/^[a-zA-Z0-9!@#$%^&*()-_=+{};:,<.>]{6,50}$/"; //а-яА-ЯёЁ
            // $r1='/[A-Z]/';  //Uppercase
            // $r2='/[a-z]/';  //lowercase
            // $r3='/[!@#$%^&*()-_=+{};:,<.>]/';  // whatever you mean by 'special char'
            // $r4='/[0-9]/';  //numbers
            // if (!preg_match($r0, $pass))
            //     $request['messages'][] = 'Стрёмный пароль!';

            // login(rememberMe = true)
            if ($request['messages'] == []) {
                $user->setPassword($pass);
                $user->token = null;
                if ($user->update() && $user->login(true)) {
                    $request['type'] = 'success';
                    $request['messages']['link'] = Url::to('/');
                    $sub = $_COOKIE['authSub'];
                    if ($sub && $sub > Yii::$app->params['subInx']) {
                        $sub = Yii::$app->params['listSubs'][$sub];
                        if ($sub && $sub['isActive'])
                            $request['messages']['link'] = $sub['link'].'personal/profile';
                    }
                } else
                    $request['messages'] = ['Ошибка авторизации, обратитесь в службу поддержки.'];
            }
        }

        return $request;
    }

    protected function errorList($rq, $err) {
        if (isset($err[0])) {
            $rq['type'] = 'error';
            $rq['messages'][] = '<li>'.$err[0].'</li>';
        }
        return $rq;
    }

    public function actionChangeOption()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new UserSettings;
        $model->id = $_POST['user_id'];

        if ($model->load($_POST)) {
            if ($model->validate() && $model->changeUserOption()) {
                $mess = 'Сохранения успешно применены!';
                $mess .= (!empty($model->_user->new_email)) ? ' Пожалуйста подтвердите Ваш новый email!' : '';
                return [ 'success' => $mess ];
            }
            return [ 'error' => $model->getErrors() ];
        }

        return [ 'error' => ['Ошибка при загрузке данных!'] ];
    }

    // CHANGE AVA
    public function actionSubChangeAva()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = User::find()->where(['id' => $_POST['user_id']])->limit(1)->one();
        if (!$user)
            return ['error' => 'Пользователь не найден!'];

        $user->image = UploadedFile::getInstanceByName('ava');
        if (!$user->image)
            return ['error' => 'Ошибка загрузки изображения!'];

        $name = $user->avaUpload($_POST['Coords'], $_POST['notCrop']);

        // $name = $user->image->name; // $user->image->baseName . '.' . $user->image->extension
        // $tmp = Yii::getAlias("@imgTeamp");

        // while (file_exists("$tmp/$name"))
        //     $name = 't'.$name;

        // $tmp = "$tmp/$name";
        // $user->image->saveAs($tmp);

        // $coords = ($_POST['Coords']) ? $_POST['Coords'] : [
        //     'W' => 250, 'H' => 250,
        //     'X' => 0, 'Y' => 0,
        // ];

        // if (!$_POST['notCrop'])
        //     Image::crop($tmp, $coords['W'], $coords['H'], [$coords['X'], $coords['Y']])
        //         ->save(Yii::getAlias("@webroot/$tmp"), ['quality' => 70]);

        // $name = $user->image->name;
        // $lPath = Yii::getAlias('@uAvaLarge');
        // while (file_exists("$lPath/$name"))
        //     $name = 's'.$name;

        // Image::getImagine()->open($tmp)
        //     ->thumbnail(new Box(250,250))
        //     ->save(Yii::getAlias("@webroot/$lPath/$name"), ['quality' => 70]);
        
        // $lPath = "$lPath/$name";

        // $sPath = Yii::getAlias('@uAvaSmall');

        // Image::getImagine()->open($tmp)
        //     ->thumbnail(new Box(150,150))
        //     ->save(Yii::getAlias("@webroot/$sPath/$name"), ['quality' => 60]);

        // $sPath = "$sPath/$name";
        // unlink($tmp);
        
        $user->image = $user->ava;
        $user->ava = $name;
        $lPath = Yii::getAlias('@uAvaLarge/');
        $sPath = Yii::getAlias('@uAvaSmall/');
        if ($user->update() && $user->image != 'no_img.jpg' && $user->image != 'del.jpg') {
            unlink($lPath.$user->image);
            unlink($sPath.$user->image);
        }

        $domain = Yii::$app->params['listSubs'][1]['link'];
        $v = mt_rand(1000, 9999);
        return [
            'success'=> [
                'large' => $domain.$lPath.$name."?v=$v",
                'small' => $domain.$sPath.$name."?v=$v",
            ]
        ];
    }

    // VKONTAKTE
    public function actionOauthVk($code, $error=null)
    {
        $this->view->title = 'Авторизация ВКонтакте | '.Yii::$app->params['shortName'];

        if (isset($error))
            return $this->render('login', [
                'page'=>'signin',
                'error'=>true,
                'errMessages'=>['Ошибка авторизации, пожалуйста попробуйте позже!']
                ]);

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl('https://oauth.vk.com/access_token')
            ->setData([
                'client_id' => $this::CLIENT_ID_VK,
                'client_secret' => $this::CLIENT_SECRET_VK,
                'code' => $code,
                'redirect_uri' => Yii::$app->params['listSubs'][1]['link'].'oauth-vk',
                'v' => '5.120',
            ])->send();

        // [access_token] => 2f959c2fa2c3182084f02bb975542e854db2082758cf28dfa530cd98f0da8a0135fd5fb145d98954ce802
        // [expires_in] => 86400
        // [user_id] => 12332145
        // [email] => test@yandex.ru
        // -----------------
        // [error] => true ?
        // [error_description] => Что-то пошло не так!
            
        if (!$response->isOk)
            return $this->render('login', [
                'page'=>'signin',
                'error'=>true,
                'errMessages'=>['Ошибка авторизации токена, пожалуйста попробуйте позже!']
                ]);

        $sub = $_COOKIE['authSub'];
        $data = $response->data;
        if ( $this->socialAuth($data['user_id'], 'VK', $data['email'], $sub) ) {
            $sub = ($sub > 1) ? Yii::$app->params['listSubs'][$sub] : 1;
            if ($sub != 1 && $sub['isActive'])
                return $this->redirect($sub['link'].'personal/profile');
            else
                return $this->goHome();
        } else
            return $this->render('login', [
                'page'=>'signin',
                'error'=>true,
                'errMessages'=>['Ошибка авторизации, попробуйте ещё раз или обратитесь в службу поддержки! (support@sdamex.ru)']
                ]);
    }

    // FACEBOOK
    public function actionOauthFb($code, $error=null)
    {
        $this->view->title = 'Авторизация Facebook | '.Yii::$app->params['shortName'];

        if (isset($error))
            return $this->render('login', [
                'page'=>'signin',
                'error'=>true,
                'errMessages'=>['Ошибка авторизации, пожалуйста попробуйте позже!']
                ]);

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl('https://graph.facebook.com/oauth/access_token')
            ->setData([
                'client_id' => $this::CLIENT_ID_FB,
                'client_secret' => $this::CLIENT_SECRET_FB,
                'code' => $code,
                'redirect_uri' => Yii::$app->params['listSubs'][1]['link'].'oauth-fb',
            ])->send();
        // [access_token] => 12332132....
        // --------------
        // [error] => [
        //     [messages] => Что-то пошло не так!
        // ]
        
        if (!$response->isOk)
            return $this->render('login', [
                'page'=>'signin',
                'error'=>true,
                'errMessages'=>['Ошибка авторизации токена, пожалуйста попробуйте позже!']
                ]);

        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl('https://graph.facebook.com/me')
            ->setData([
                'access_token' => $response->data['access_token'],
                'fields' => 'id, first_name, last_name, email', //gender, 
            ])->send();
        // [id] => 12331231231251235
        // [first_name] => Иван
        // [last_name] => Иванов
        // [email] => test@yandex.ru

        if (!$response->isOk)
            return $this->render('login', [
                'page'=>'signin',
                'error'=>true,
                'errMessages'=>['Отказано в получение данных, пожалуйста попробуйте позже!']
                ]);

        $sub = $_COOKIE['authSub'];
        $data = $response->data;
        if ( $this->socialAuth($data['id'], 'FB', $data['email'], $sub, ['name'=>$data['first_name'], 'surname'=>$data['last_name']]) ) {
            $sub = ($sub > 1) ? Yii::$app->params['listSubs'][$sub] : 1;
            if ($sub != 1 && $sub['isActive'])
                return $this->redirect($sub['link'].'personal/profile');
            else
                return $this->goHome();
        } else
            return $this->render('login', [
                'page'=>'signin',
                'error'=>true,
                'errMessages'=>['Ошибка авторизации, попробуйте ещё раз или обратитесь в службу поддержки! (support@sdamex.ru)']
                ]);
    }

    // GOOGLE
    public function actionOauthGl($code, $error=null)
    {
        $this->view->title = 'Авторизация Google | '.Yii::$app->params['shortName'];

        if (isset($error))
            return $this->render('login', [
                'page'=>'signin',
                'error'=>true,
                'errMessages'=>['Ошибка авторизации, пожалуйста попробуйте позже!']
                ]);

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl('https://accounts.google.com/o/oauth2/token')
            ->setData([
                'client_id' => $this::CLIENT_ID_GL,
                'client_secret' => $this::CLIENT_SECRET_GL,
                'code' => $code,
                'redirect_uri' => Yii::$app->params['listSubs'][1]['link'].'oauth-gl',
                'grant_type' => 'authorization_code',
            ])->send();
        // [access_token] => 2f959c2fa....
        // ---------------
        // [error] => invalid_client
        // [error_description] => Unauthor

        if (!$response->isOk)
            return $this->render('login', [
                'page'=>'signin',
                'error'=>true,
                'errMessages'=>['Ошибка авторизации токена, пожалуйста попробуйте позже!']
                ]);

        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl('https://www.googleapis.com/oauth2/v1/userinfo')
            ->setData([
                'access_token' => $response->data['access_token'],
            ])->send();
        // [id] => 115373650031543310868
        // [email] => example@gmail.com
        // [verified_email] => 1
        // [name] => Иван Иванов
        // [given_name] => Иван
        // [family_name] => Иванов
        // [picture] => https://lh3.googleusercontent.com/a-/AOh14GhilNmpc2nGe5tMgg6x0JIhnklz9-tSFnvpvt8LQQ
        // [locale] => ru

        if (!$response->isOk)
            return $this->render('login', [
                'page'=>'signin',
                'error'=>true,
                'errMessages'=>['Отказано в получение данных, пожалуйста попробуйте позже!']
                ]);

        $sub = $_COOKIE['authSub'];
        $data = $response->data;
        if ( $this->socialAuth($data['id'], 'GL', $data['email'], $sub, ['name'=>$data['given_name'], 'surname'=>$data['family_name']]) ) {
            $sub = ($sub > 1) ? Yii::$app->params['listSubs'][$sub] : 1;
            if ($sub != 1 && $sub['isActive'])
                return $this->redirect($sub['link'].'personal/profile');
            else
                return $this->goHome();
        } else
            return $this->render('login', [
                'page'=>'signin',
                'error'=>true,
                'errMessages'=>['Ошибка авторизации, попробуйте ещё раз или обратитесь в службу поддержки! (support@sdamex.ru)']
                ]);
    }

    protected function socialAuth($id, $type, $email, $sub=0, $params=null)
    {
        $social = UserSocial::find()->where(['social_id'=>$id.$type])->limit(1)->one();

        if ($social && $social->user)
            return $social->user->login(true);

        $user = User::findByEmail($email);
        if ($user == null) {
            $user = new User;

            $split = explode('@',$email);
            $user->username = $user->validateUsername($split[0]);

            $mainInx = Yii::$app->params['subInx'];
            $stats = [ $mainInx => [] ];
            if ($sub > $mainInx)
                $stats[$sub] = User::DEF_STAT;

            $user->email = $email;
            $user->name = $params['name'];
            $user->surname = $params['surname'];
            $user->status = UserStatus::ACTIVE;
            $user->statistics = json_encode($stats);
            $user->generateAuthKey();
            $user->save();

            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole('user');
            $auth->assign($authorRole, $user->getId());
        }

        $social = new UserSocial;
        $social->social_id = $id.$type;
        $social->user_id = $user->id;
        $social->type = $type;
        $social->save();

        return $user->login(true);
    }
}

 ?>
