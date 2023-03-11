<?php

namespace app\modules\closedoor\controllers;

use Yii;
use app\models\course\{Course};
use app\models\webinar\{Webinar, Member};
use app\controllers\AppController;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class WebinarController extends AppController
{
    public function behaviors()
    {
        return [
            // 'verbs' => [
            //     'class' => \yii\filters\VerbFilter::className(),
            //     'actions' => [
            //         'index'             => ['GET'],
            //         'change-phrase'      => ['POST'],
            //     ],
            // ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                // 'except' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['speaker', 'mainTeacher'],
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
            'model' => Webinar::find()->asArray()->all(),
        ]);
    }

    public function actionDetails($id = 0)
    {
        $model = ($id > 0) ? Webinar::find()->where(['id'=>$id])->with([
                'courses' => function ($query) {
                    $query->select(['id', 'subject_id']);
                }
            ])->one() : new Webinar();
        $courses = Course::find()->select(['id', 'subject_id', 'title'])->all();
        
        if (empty($model->author_id))
            $model->author_id = Yii::$app->user->id;

        if ( $model->load($_POST) ) {
            $model->live_link = $this->frameLinkCreation($model->live_link);
            $model->video_link = $this->frameLinkCreation($model->video_link);
            $model->courses_id = (isset($_POST['Webinar']['courses_id'])) ? json_decode($_POST['Webinar']['courses_id'],true) : [];
            $model->links = json_encode((isset($_POST['Webinar']['links'])) ? $_POST['Webinar']['links'] : []);

            $gmt = $_COOKIE['GMT'];
            $model->start_at = strtotime($model->strDate);
            // приводим время к utc = 0
            if ($gmt)
                $model->start_at = strtotime(-1*$gmt.' hours', $model->start_at);
            $model->strDate = date('d.m.Y H:i', $model->start_at);

            $isSoon = ($model->start_at <= (time()+24*60*60));
            $model->image = UploadedFile::getInstance($model,'image');
            if ( ($model->id > 0 || !$isSoon) && isset($model->image) ) {
                $model->settings = [
                    'large' => [
                        'path' => Yii::getAlias("@webnAvaLarge"),
                        'size' => [
                            'w' => 1600,
                            'h' => 1200,
                        ],
                    ],
                    'small' => [
                        'path' => Yii::getAlias("@webnAvaSmall"),
                        'size' => [
                            'w' => 400,
                            'h' => 300,
                        ],
                    ],
                ];
                $model->ava = $model->imageUpload($model->ava);
            } else if (!$model->ava)
                $model->ava = 'no_img.jpg';
            
            if ($id == 0) {
                $model->save();

                $member = new Member;
                $member->webinar_id = $model->id;
                $member->user_id = $model->author_id;
                $member->save();

                return $this->redirect(['details', 'id'=>$model->id]);
            } else
                $model->update();

            // Создаём линки с курсами
            $model->unlinkAll('courses', true);
            foreach ($courses as $crs) {
                if (in_array($crs->id, $model->courses_id))
                    $model->link('courses', $crs);
            }
        }

        if ($id > 0) {
            $gmt = $_COOKIE['GMT'];
            if ($gmt)
                $model->start_at = strtotime($gmt.' hours', $model->start_at);
            $model->strDate = date('d.m.Y H:i', $model->start_at);
        }

        foreach (Yii::$app->params['listSubs'] as $sId => $sVal) {
            if ($sVal['isActive'])
                $model->subjects[$sId] = ($sId == 1) ? 'ВСЕ' : $sVal['lable'];
        }

        return $this->render('details', [
            'model' => $model,
            'courses' => $courses
        ]);
    }

    public function actionDeleteWebinar()
    {
        if (!Yii::$app->user->can('admin'))
            return 0;
            
        $id = Yii::$app->request->post('id');
        if ($id > 0) {
            $model = $this->findWebinar($id);
            if ($model != null) {
                $model->settings = [
                    'large' => [
                        'path' => Yii::getAlias("@webnAvaLarge"),
                    ],
                    'small' => [
                        'path' => Yii::getAlias("@webnAvaSmall"),
                    ],
                ];
                $model->deleteOldImg($model->ava);
                $model->delete();
                return 1;
            }
        }
        return 0;
    }

    protected function findWebinar($id, $with = false)
    {
        // if ($with) {
        //     $model = Webinar::find()->where(['webinar.id'=>$id])->joinWith([''])->limit(1)->one();
        // } else {
            $model = Webinar::findOne($id);
        // }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страница не найдена!');
    }
}
