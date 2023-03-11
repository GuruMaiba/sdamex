<?php

namespace app\modules\closedoor\controllers;

use Yii;
use yii\helpers\Url;
use app\models\{User, Teacher};
use app\models\promoter\{Promoter, Code};
use app\models\course\{Course, Module, Lesson, Student};
use app\models\search\UserSearch;
use app\models\form\AdminUser;
use app\controllers\AppController;
use yii\db\Query;
use yii\web\{Response, UploadedFile, NotFoundHttpException};
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends AppController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new UserSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // дёргать пользователей по роли
        // $operatoriIds = Yii::$app->authManager->getUserIdsByRole('operator');
        // $operator = User::find()->where(['id' => $operatoriIds])->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate($id = 0)
    {
        $model = new AdminUser;

        if ($id > 0)
            $model->getValue($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                // подгружаем картинку для манипуляций)) ^^
                $user = $model->findUser($model->id);
                $user->image = UploadedFile::getInstance($model,'image');
                $model->coords = $_POST['Coords'];
                
                // изменяем настройки пользователя
                $model->changeUserOption();

                return $this->redirect(['user/create/'.$user->id]);
            }
        }

        foreach (Yii::$app->authmanager->getRoles() as $key => $obj) {
            if ($key != 'MegaAdmin')
                $model->roles[$key] = $key;
        }

        if ($id > 0) {
            $courses = Course::find()->select(['id', 'subject_id', 'title'])->where(['free'=>0, 'publish' => 1])->asArray()->all();
            $accesses = Student::find()->where(['learner_id'=>$id])->andWhere(['>','end_at',time()])->asArray()->all();
        }

        return $this->render('create', [
            'model' => $model,
            'courses' => $courses,
            'accesses' => $accesses,
            'code' => ($model->role != 'user') ? new Code : null,
        ]);
    }

    public function actionAddCourse()
    {
        if (empty($_POST['user_id']) || empty($_POST['course_id']))
            return 0;

        $model = Student::find()->where(['learner_id'=>$_POST['user_id'], 'course_id'=>$_POST['course_id']])->limit(1)->one();
        
        $now = time();
        if ($model == null || $model->end_at < $now) {
            if (!$model) {
                $model = new Student;
                $model->learner_id = $_POST['user_id'];
                $model->course_id = $_POST['course_id'];
            }

            $model->start_at = $now;
            $model->end_at = $now + (30*24*3600);

            if (!$model->save())
                return 0;

            $gmt = (!empty($_COOKIE['GMT'])) ? ($_COOKIE['GMT']*3600) : 0;

            return date('d.m.Y H:i', $gmt + $model->end_at);
        } else {
            $model->delete();
            return 1;
        }
    }

    public function actionAccessTime()
    {
        if (empty($_POST['user_id']) || empty($_POST['course_id']))
            return 0;

        $model = Student::find()->where(['learner_id'=>$_POST['user_id'], 'course_id'=>$_POST['course_id']])->limit(1)->one();

        if (!$model)
            return 0;

        $gmt = $_COOKIE['GMT'];
        $model->end_at = strtotime($_POST['date']);
        if ($gmt)
            $model->end_at = strtotime(-1*$gmt.' hours', $model->end_at);
        if (!$model->save())
            return 0;

        return 1;
    }

    public function actionTeacherOption()
    {
        $model = Teacher::findOne($_POST['Teacher']['user_id']);
        
        if ($model != null && $model->load($_POST)) {
            if ($model->validate() && $model->update())
                return $this->redirect(['user/create/'.$model->user_id]);
        }
    }

    public function actionCode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $_POST['Code']['code'] = str_replace(' ', '', $_POST['Code']['code']);
        $_POST['Code']['end_at'] = (!empty($_POST['Code']['str_date'])) ? strtotime($_POST['Code']['str_date']) : null;
        $pCode = $_POST['Code'];

        if (strlen($pCode['code']) <= 3)
            return 0;

        $code = Code::find()->where(['code'=>$pCode['code']])->limit(1)->one();
        if (!$code) {
            if (empty($pCode['old_code']))
                $invite = new Code;
            else
                $invite = Code::find()->where(['code'=>$pCode['old_code']])->limit(1)->one();
        } else
            $invite = $code;

        if ($invite->load($_POST) && $invite->validate()) {
            $invite->save();
            return $this->renderPartial('_invitecode', [
                'codes' => [ $invite ],
            ]);
        }
        return 0;
    }

    public function actionDelCode()
    {
        $code = Code::find()->where(['code'=>$_POST['old_code']])->limit(1)->one();
        if ($code)
            $code->delete();
        
        return 1;
    }

    public function actionCourse($id = 0, $user_id = 0)
    {
        if ($id <= 0 || $user_id <= 0)
            throw new NotFoundHttpException('Отсутствуют обязательные парамметры!');

        $model = ProgressCourse::find()->where(['course_id'=>$id, 'learner_id'=>$user_id])
        ->limit(1)->one();
        if ($model == null)
            $model = new ProgressCourse();

        if ($model->load(Yii::$app->request->post())) {
            $model->setDate();
            $model->count_points = 0;
            // return $this->debug($model);
            $model->save();
            return $this->redirect(['user/create/'.$model->learner_id]);
        }

        if ($model->id == 0) {
            // Находим курс
            $course = Course::find()
                ->where(['id'=>$id, 'free'=>false, 'publish'=>true])
                ->limit(1)->one();
            if ($course == null)
                throw new NotFoundHttpException('Курс не найден!');

            // Ищем подходящий модуль
            $module = Module::find()
                ->where(['course_id'=>$id, 'free'=>false, 'publish'=>true])
                ->orderBy('place')->limit(1)->one();
            if ($module == null)
                throw new NotFoundHttpException('Подходящий Модуль не найден!');

            // Ищем подходящий урок
            $lesson = Lesson::find()
                ->where(['module_id'=>$module->id, 'free'=>false, 'publish'=>true])
                ->orderBy('place')->limit(1)->one();
            if ($lesson == null)
                throw new NotFoundHttpException('Подходящий Урок не найден!');

            $model->learner_id = $user_id;
            $model->course_id = $id;
            $model->module_id = $module->id;
            $model->lesson_id = $lesson->id;
            $model->start_at = time();
            $model->end_at = strtotime('1 month', $model->start_at);
        }
        $model->getDate();

        return $this->render('course', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        // $model->delete_account = true;
        // $model->update();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
