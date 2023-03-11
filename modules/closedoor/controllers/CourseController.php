<?php

namespace app\modules\closedoor\controllers;

use Yii;
use app\models\course\{Course, Module, Lesson, Progress};
use app\models\exam\{Examtest,Examwrite};
use app\controllers\AppController;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
// use yii\filters\VerbFilter;

class CourseController extends AppController
{
    public function behaviors()
    {
        return [
            // 'verbs' => [
            //     'class' => \yii\filters\VerbFilter::className(),
            //     'actions' => [
            //         'index'             => ['GET'],
            //         'change-phrase'      => ['POST'],
            //         'appointment'       => ['POST'],
            //         'change-skype'      => ['POST'],
            //         'set-videolink'     => ['POST'],
            //         'change-time'       => ['POST'],
            //         'edit-time'         => ['POST'],
            //         'change-timelock'   => ['POST'],
            //         'change-aboutme'    => ['POST'],
            //         'review'            => ['POST'],
            //         'review-delete'     => ['POST'],
            //     ],
            // ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                // 'except' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['mainTeacher'],
                    ],
                    // [
                    //     'allow' => true,
                    //     'actions' => ['change-phrase', 'set-videolink', 'change-time', 'edit-time', 'change-timelock', 'change-aboutme'],
                    //     'roles' => ['updateProfile'],
                    //     'roleParams' => ['id' => Yii::$app->request->post('id')],
                    // ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'model' => Course::find()->orderBy('id')->asArray()->all(),
        ]);
    }

    public function actionDetails($id = 0)
    {
        $model = ($id > 0)
            ? Course::find()->where(['id'=>$id])->with([
                'modules' => function ($query) { $query->orderBy('place'); },
                'modules.lessons'
            ])->limit(1)->one()
            : new Course;
        if (empty($model->author_id))
            $model->author_id = Yii::$app->user->identity->id;

        if ($model->load($_POST)) {
            
            $model->image = UploadedFile::getInstance($model,'image');
            if ( isset($model->image) ) {
                $model->settings = [
                    'large' => [
                        'path' => Yii::getAlias("@crsAvaLarge"),
                        'size' => [
                            'w' => 1600,
                            'h' => 1200,
                        ],
                    ],
                    'small' => [
                        'path' => Yii::getAlias("@crsAvaSmall"),
                        'size' => [
                            'w' => 850,
                            'h' => 700,
                        ],
                    ],
                ];
                $model->ava = $model->imageUpload($model->ava);
            }

            if ($model['free'] && $id != 0) {
                foreach ($model->modules as $module) {
                    $module->free = 1;
                    $module->update();
                    foreach ($module->lessons as $lesson) {
                        $lesson->free = 1;
                        $lesson->update();
                    }
                }
            }

            $model->save();
            return $this->redirect(['details', 'id'=>$model->id]);
        }

        foreach (Yii::$app->params['listSubs'] as $sId => $sVal) {
            if ($sVal['isActive'])
                $model->subjects[$sId] = $sVal['name'];
        }

        return $this->render('details', [
            'model' => $model,
        ]);
    }

    public function actionDeleteCourse()
    {
        if (!Yii::$app->user->can('admin'))
            return 0;
        return $this->deleteModel(Yii::$app->request->post('id'), 'course');
    }

    public function actionModule($course_id, $id = 0)
    {
        $model = ($id > 0)
            ? Module::find()->where(['id'=>$id])->with(['lessons' => function ($query) { $query->orderBy('place'); }])->limit(1)->one()
            : new Module;
        $course = Course::find()->select(['id', 'free'])->where(['id'=>$course_id])->asArray()->limit(1)->one();
        if ($id == 0) {
            $model->course_id = $course_id;
            $model->free = $course['free'];
            $model->place = Module::find()->having(['course_id'=>$course_id])->count() + 1;
        }

        if ($model->load($_POST)) {
            $model->image = UploadedFile::getInstance($model,'image');
            if ( isset($model->image) ) {
                $model->settings = [
                    'images' => [
                        'path' => Yii::getAlias("@crsAvaModule"),
                        'size' => [
                            'w' => 1600,
                            'h' => 1200,
                        ],
                    ],
                ];
                $model->ava = $model->imageUpload($model->ava);
            }
            if ($course['free'])
                $model->free = 1;
            $model->save();
            return $this->redirect(['module', 'id'=>$model->id, 'course_id'=>$course_id]);
        }

        return $this->render('module', [
            'model' => $model,
        ]);
    }

    public function actionDeleteModule()
    {
        if (!Yii::$app->user->can('admin'))
            return 0;
        return $this->deleteModel(Yii::$app->request->post('id'), 'module');
    }

    public function actionLesson($module_id, $course_id, $id = 0)
    {
        $model = ($id > 0) ? $this->findLesson($id, true) : new Lesson;
        $module = Module::find()->select(['id', 'free'])->where(['id'=>$module_id])->asArray()->limit(1)->one();

        if ($id == 0) {
            $model->module_id = $module_id;
            $model->free = $module['free'];
            $model->place = Lesson::find()->having(['module_id'=>$module_id])->count() + 1;
        }

        if ($model->load($_POST)) {
            $model->video = $this->frameLinkCreation($model->video);
            $model->links = json_encode((isset($_POST['Lesson']['links'])) ? $_POST['Lesson']['links'] : []);
            if ($module['free'])
                $model->free = 1;
            $model->save();
            return $this->redirect(['lesson', 'id'=>$model->id, 'course_id'=>$course_id, 'module_id'=>$module_id]);
        }

        return $this->render('lesson', [
            'model' => $model,
            'course' => $course_id,
        ]);
    }

    public function actionDeleteLesson()
    {
        if (!Yii::$app->user->can('admin'))
            return 0;
        return $this->deleteModel(Yii::$app->request->post('id'), 'lesson');
    }

    protected function findCourse($id, $withModules = false)
    {
        if ($withModules) {
            return $model = Course::find()->where(['id'=>$id])->with('modules')->limit(1)->one();
        } else {
            return $model = Course::findOne($id);
        }
    }

    protected function findModule($id, $withLessons = false)
    {
        if ($withLessons) {
            return $model = Module::find()->where(['id'=>$id])->with('lessons')->limit(1)->one();
        } else {
            return $model = Module::findOne($id);
        }
    }

    protected function findLesson($id)
    {
        return $model = Lesson::findOne($id);
    }

    protected function deleteModel($id, $type)
    {
        if ($id > 0 && $type != null) {
            switch ($type) {
                case 'course':
                    $model = $this->findCourse($id);
                    break;

                case 'module':
                    $model = $this->findModule($id);
                    break;

                case 'lesson':
                    $model = $this->findLesson($id);
                    break;

                default:
                    $model = null;
                    break;
            }

            if ($model != null) {
                $model->delete();
                return 1;
            }
        }
        return 0;
    }
}
