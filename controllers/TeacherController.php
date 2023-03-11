<?php

namespace app\controllers;

use Yii;
// use app\models\course\{Course, Module, Lesson};
use app\controllers\AppController;

class TeacherController extends AppController
{
    // public function behaviors()
    // {
    //     return [
    //         [
    //             'class' => 'yii\filters\PageCache',
    //             'only' => ['index'],
    //             'duration' => 3600, //3600
    //             // 'variations' => [
    //             //     \Yii::$app->language,
    //             // ],
    //             // 'dependency' => [
    //             //     'class' => 'yii\caching\DbDependency',
    //             //     'sql' => 'SELECT COUNT(*) FROM post',
    //             // ],
    //         ],
    //     ];
    // }

    public function actionIndex()
    {
        $this->view->title = 'Как стать онлайн учителем, ведущим курса ОГЭ и ЕГЭ на партнёрских условиях | '.Yii::$app->params['shortName'];
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => 'Стань онлайн учителем ОГЭ, ЕГЭ и получай процент с продаж. На всех этапах подготовки курса, мы поможем и окажем содействие, вливайся в солнечную команду SDAMEX!',
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => 'огэ, егэ, учитель, онлайн курс, ведущий курса, стать учителем, партнёрские условия, '.Yii::$app->params['commonKeyWords'],
        ]);
        return $this->render('index');
    }

    public function actionSendRequest()
    {
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']))
            return 0;

        // send mail
        $mail = Yii::$app->mailer->compose('becomeTeacher', [
                'name'=>$_POST['name'],
                'email'=>$_POST['email'],
                'phone'=>$_POST['phone'],
                'time'=>$_POST['time'],
            ]) // result rendering view
            ->setFrom([ Yii::$app->params['mailingEmail'] => Yii::$app->params['shortName'] . ' | Рассылка' ])
            ->setTo(Yii::$app->params['yandexEmail'])
            ->setSubject('Новая заявка на должность учителя')
            ->send();

        return 1;
    }
}
