<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\{Response};

class SiteController extends AppController
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => false
            ],
            // 'captcha' => [
            //     'class' => 'yii\captcha\CaptchaAction',
            //     'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            // ],
        ];
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'index'             => ['GET'],
                    'back-call'         => ['POST'],
                    'terms'             => ['GET'],
                    'privacy'           => ['GET'],
                    'contract-offer'    => ['GET'],
                    'save-file'         => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['save-file'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['save-file'],
                        'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->request->post('secretKey') === Yii::$app->params['secretKey']);
                        },
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['save-file'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    // Главная страница
    public function actionIndex()
    {
        $this->view->title = Yii::$app->params['shortName'].' | Подготовка к экзаменам: ОГЭ, ЕГЭ';
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => 'Мы создаем систему, способную беспрепятственно подготовить любого заинтересованного студента к экзаменам, в особенности к ОГЭ и ЕГЭ. SDAMEX — cамый крупный проект подготовки к экзаменам в России 2020.',
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => 'подготовка, экзамены, ОГЭ, ЕГЭ, '.Yii::$app->params['commonKeyWords'],
        ]);
        return $this->render('index');
    }

    public function actionBackCall()
    {
        if (empty($_POST['phone']))
            return 0;

        // send mail
        $mail = Yii::$app->mailer->compose('backCall', [
                'phone'=>$_POST['phone'],
                'time'=>$_POST['time'],
            ]) // result rendering view
            ->setFrom([ Yii::$app->params['mailingEmail'] => Yii::$app->params['shortName'] . ' | Рассылка' ])
            ->setTo('sales@sdamex.ru')
            ->setSubject('Запрос на обратный звонок!')
            ->send();

        return 1;
    }

    // Пользовательское соглашение
    public function actionTerms()
    {
        $this->layout = 'document';
        $this->view->title = 'Пользовательское соглашение | '.Yii::$app->params['shortName'];
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => 'Пользовательское соглашение для сайта SDAMEX.RU и всех поддоменов включительно.',
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => 'пользовательское, соглашение, пользовательское соглашение, '.Yii::$app->params['commonKeyWords'],
        ]);
        return $this->render('terms');
    }

    // Политика конфиденциальности
    public function actionPrivacy()
    {
        $this->layout = 'document';
        $this->view->title = 'Политика конфиденциальности | '.Yii::$app->params['shortName'];
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => 'Политика конфиденциальности для сайта SDAMEX.RU и всех поддоменов включительно.',
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => 'политика, конфиденциальности, политика конфиденциальности, '.Yii::$app->params['commonKeyWords'],
        ]);
        return $this->render('privacy');
    }

    public function actionContractOffer()
    {
        $this->layout = 'document';
        $this->view->title = 'Договор оферты | '.Yii::$app->params['shortName'];
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => 'Договор оферты для сайта SDAMEX.RU и всех поддоменов включительно.',
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => 'договор, оферты, договор оферты, '.Yii::$app->params['commonKeyWords'],
        ]);
        return $this->render('offer');
    }

    public function actionSaveFile()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            if ($fileExtension === 'zip') {
                while ( file_exists(Yii::getAlias("@fileWrites/$fileName")) )
                    $fileName = substr(str_shuffle($this->permitted_chars), 0, 16).'.zip';

                if(move_uploaded_file($fileTmpPath, Yii::getAlias("@fileWrites/$fileName")))
                    return ['req'=>$fileName];
            }
            
            return ['req'=>0];
        }
    }

    /**
     * Displays homepage
     * @return string
     */
    // public function actionIndex()
    // {
    //     return $this->render('index');
    // }
}
