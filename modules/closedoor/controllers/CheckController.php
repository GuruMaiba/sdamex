<?php

namespace app\modules\closedoor\controllers;

use Yii;
use app\models\{User, Theme};
use app\models\course\Lesson;
use app\models\exam\{Fullexam, Section, Exercise, Result};
use app\models\exam\write\{Write, Reply};
use app\controllers\AppController;
use app\components\ExamType;
use yii\web\HttpException;
use yii\helpers\Url;
use yii\httpclient\Client;
// use yii\web\NotFoundHttpException;
// use yii\filters\VerbFilter;

class CheckController extends AppController
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
                        'roles' => ['checkTeacher'],
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

    public function actionIndex($type = 'write')
    {
        if ($type == 'write')
            $model = Reply::find()->select(['id', 'user_id', 'examwrite_id'])
                ->where(['and', ['check'=>0], ['in','teacher_id', [null,Yii::$app->user->identity->id]]])
                ->with([
                    'write' => function ($query) {
                        $query->select(['id', 'exercise_id', 'lesson_id', 'webinar_id']);
                    },
                    'write.exercise' => function ($query) { $query->select(['id', 'section_id', 'name']); },
                    'write.exercise.section' => function ($query) { $query->select(['id', 'fullexam_id', 'name']); },
                    'write.exercise.section.fullexam' => function ($query) { $query->select(['id', 'name']); },
                    'write.lesson' => function ($query) { $query->select(['id', 'module_id', 'title']); },
                    'write.lesson.module' => function ($query) { $query->select(['id', 'course_id', 'title']); },
                    'write.lesson.module.course' => function ($query) { $query->select(['id', 'title']); },
                    // DEBUG: 'webinar'
                ])->asArray()->all();
        else //if ($type == 'exam')
            $model = Result::find()->select(['id', 'user_id', 'fullexam_id'])
                ->where(['and', ['check'=>0], ['in','teacher_id', [null,Yii::$app->user->identity->id]]])->with([
                    'fullexam' => function ($query) {
                        $query->select(['id', 'name']);
                    }
                ])->asArray()->all(); 

        return $this->render('index', [
            'type' => $type,
            'model' => $model
        ]);
    }

    public function actionPractical($id = 0)
    {
        $isPost = Yii::$app->request->isPost;

        if ($id < 1)
            throw new HttpException(404);
        
        $model = Reply::find()->where(['id'=>$id,'check'=>0])->with(['write'])->limit(1)->one();

        if (!$model)
            throw new HttpException(404);

        $isExercise = ($model->write->exercise_id > 0);
        $isLesson = ($model->write->lesson_id > 0);
        $subject = 0;
        if ($isExercise) {
            $link = Exercise::find()->select(['id', 'section_id', 'fullexam_points'])
                ->where(['id'=>$model->write->exercise_id])
                ->with([
                    'section' => function ($query) { $query->select(['id', 'fullexam_id']); },
                    'section.fullexam' => function ($query) { $query->select(['id', 'subject_id']); },
                ])->asArray()->limit(1)->one();
            $subject = $link['section']['fullexam']['subject_id'];
        } else if ($isLesson) {
            $link = Lesson::find()->select(['id', 'module_id'])->where(['id'=>$model->write->lesson_id])
                ->with([
                    'module' => function ($query) { $query->select(['id', 'course_id']); },
                    'module.course' => function ($query) { $query->select(['id', 'subject_id']); },
                ])->asArray()->limit(1)->one();
            $subject = $link['module']['course']['subject_id'];
        }
        $subject = Yii::$app->params['listSubs'][$subject];

        if (!$link)
            throw new HttpException(404);

        if (!$model->teacher_id) {
            $model->teacher_id = Yii::$app->user->identity->id;
            $model->update();
        }

        $model->exp = $model->write->exp;
        if ($isExercise)
            $model->points = ($link) ? $link['fullexam_points'] : 0;
        else if ($isLesson)
            $model->points = 1;
        $writeThemes = json_decode($model->write->themes, true);

        if ($isPost) {
            $reply = $_POST['Reply'];
            $themes = ($_POST['Write']['themes']) ? json_decode($_POST['Write']['themes']) : null;
            $action = $_POST['action'];
            if ($reply['teacher_comment'] == ''
                || $reply['exp'] == '' || $reply['exp'] < 0 || $reply['exp'] > $model->exp
                || (($reply['points'] == '' || $reply['points'] < 0 || $reply['points'] > $model->points) && $isExercise))
                throw new HttpException(403, 'В Ваших данных допущена ошибка');

            $file = $model->archive_file;
            $model->check = 1;
            $model->check_at = time();
            $model->exp = $reply['exp'];
            $model->points = ($isExercise) ? $reply['points'] : (($action == 'right')?1:0);
            $model->teacher_comment = $reply['teacher_comment'];
            $model->archive_file = null;
            $model->update();

            if (!$subject || $subject['name'] == 'MAIN' || file_exists(Yii::getAlias("@fileWrites/$file"))) {
                unlink(Yii::getAlias("@fileWrites/$file"));
            } else {
                $client = new Client();
                $response = $client->createRequest()
                    ->setMethod('post')
                    ->setUrl($subject['link'].'exams/delete-homework')
                    ->setData([
                        'file' => [$file],
                        'appKey' => Yii::$app->params['secretKey'],
                    ])->send();
            }

            $user = User::find()->where(['id'=>$model->user_id])->limit(1)->one();
            if ($user) {
                if ($isExercise) {
                    $stats = Exercise::STATS_EXAM_ARR;
                    $stats['id'] = $link['section']['fullexam_id'];
                    foreach ((array)$writeThemes as $id) {
                        if (in_array($id, $themes))
                            $stats['themes']['corr'][] = $id;
                        else
                            $stats['themes']['err'][] = $id;
                    }

                    $task_stat = Exercise::STAT_EXAM_TASK;
                    $task_stat['task'] = $model->examwrite_id;
                    $task_stat['corr'] = ($action == 'right')?1:0;
                    $stats['sections'][$link['section']['id']]['exercises'][$link['id']] = $task_stat;

                    $user->statistics = $this->exeStatUpdate($link['section']['fullexam']['subject_id'], json_decode($user->statistics, true), $stats, true);
                    $user->addExp($model->exp);
                } else if ($isLesson) {
                    $stats = json_decode($user->statistics, true);
                    $statLesson = (array)$stats[$link['module']['course']['subject_id']]['courses'][$link['module']['course_id']]['modules'][$link['module_id']]['lessons'][$link['id']];
                    $statLesson['end'] = ($statLesson['end'] || $type == 'test') ? 1 : 0;
        
                    // добавляем опыт
                    if (isset($statLesson['write']['exp'])) {
                        if ($statLesson['write']['exp'] > $model->exp)
                            $model->exp = $statLesson['write']['exp'];
                        else
                            $user->addExp($model->exp - $statLesson['write']['exp']);
                    } else
                        $user->addExp($model->exp);
                        
                    $statLesson['write'] = [
                        'completed' => 1,
                        'exp' => $model->exp,
                        'right' => ($action == 'right' || $statLesson['write']['right']) ? 1 : 0,
                    ];
                    
                    $stats[$link['module']['course']['subject_id']]['courses'][$link['module']['course_id']]['modules'][$link['module_id']]['lessons'][$link['id']] = $statLesson;
                    $user->statistics = json_encode($stats);
                }
                $user->update();
            } else
                $model->delete();
            
            return $this->redirect(['index']);
        } // end $isPost

        return $this->render('practical', [
            'model' => $model,
            'subject' => $subject,
            'wThemes' => $writeThemes,
            'themes' => Theme::find()->where(['in', 'id', $writeThemes])->asArray()->all(),
        ]);
    }

    public function actionExam($id = 0)
    {
        $isPost = Yii::$app->request->isPost;
        $user_id = Yii::$app->user->identity->id;

        if ($id < 1)
            throw new HttpException(404);
        
        $model = Result::find()->where(['id'=>$id,'check'=>0])->with(['fullexam'])->limit(1)->one();

        if (!$model || $model->check)
            throw new HttpException(404);

        if ($model->teacher_id > 0 && $user_id != $model->teacher_id)
            throw new HttpException(403, 'Эта работа уже проверяется!');

        if (!$model->teacher_id) {
            $model->teacher_id = $user_id;
            $model->update();
        }

        $answers = json_decode($model->answers, true);
        $writesId = [];
        $writesAns = [];
        foreach ($answers['sections'] as $sec_id => $section) {
            foreach ($section['exercises'] as $exe_id => $exercise) {
                if ($exercise['type'] == ExamType::WRITE) {
                    $writesId[] = $exercise['write']['id'];
                    $writesAns[$exercise['write']['id']] = [
                        'answer' => $exercise['write']['answer'],
                        'file' => $exercise['write']['archiveFile'],
                    ];
                }
            }
        }

        if ($writesId === []) {
            $model->check = 1;
            $model->teacher_id = null;
            $model->update();
            return $this->redirect(['index']);
        }

        $subject = Yii::$app->params['listSubs'][$model->fullexam->subject_id];
        $writes = Write::find()->where(['in', 'id', $writesId])->with([
                'exercise' => function ($query) { $query->select(['id', 'section_id', 'fullexam_points']); },
            ])->asArray()->all();
        // return $this->debug($writesAns);

        if ($isPost) {
            $comment = $_POST['Result']['teacher_comment'];
            if ($comment == '')
                throw new HttpException(403, 'В Ваших данных допущена ошибка');

            $user = User::find()->where(['id'=>$model->user_id])->limit(1)->one();
            $fullStats = json_decode($user->statistics, true);
            $writes_post = $_POST['Write'];
            $exp = 0;
            $points = 0;
            $files = [];

            foreach ($writes as $wrt) {
                $w_post = $writes_post[$wrt['id']];
                $exp_post = ($w_post['exp'] > 0 && $w_post['exp'] <= $wrt['exp']) ? $w_post['exp'] : 0;
                $points_post = ($w_post['points'] > 0 && $w_post['points'] <= $wrt['exercise']['fullexam_points']) ? $w_post['points'] : 0;
                $exp += $exp_post;
                $points += $points_post;

                $ansWrt = $answers['sections'][$wrt['exercise']['section_id']]['exercises'][$wrt['exercise_id']]['write'];
                $ansWrt['exp'] = $exp_post;
                $ansWrt['points'] = $points_post;
                $ansWrt['right'] = ($w_post['right'])?1:0;
                $files[] = $ansWrt['archive_file'];
                unset($ansWrt['archive_file']);
                $answers['sections'][$wrt['exercise']['section_id']]['exercises'][$wrt['exercise_id']]['write'] = $ansWrt;

                if ($user) {
                    $stats = Exercise::STATS_EXAM_ARR;
                    $stats['id'] = $model->fullexam_id;
                    $wThemes = json_decode($wrt['themes'], true);
                    $themes = json_decode($writes_post[$wrt['id']]['themes'], true);
                    foreach ((array)$wThemes as $tId) {
                        if (in_array($tId, $themes))
                            $stats['themes']['corr'][] = $tId;
                        else
                            $stats['themes']['err'][] = $tId;
                    }

                    $task_stat = Exercise::STAT_EXAM_TASK;
                    $task_stat['task'] = $wrt['id'];
                    $task_stat['corr'] = ($w_post['right'])?1:0;
                    $stats['sections'][$wrt['exercise']['section_id']]['exercises'][$wrt['exercise_id']] = $task_stat;

                    $fullStats = $this->exeStatUpdate($model->fullexam->subject_id,$fullStats, $stats, true, true);
                }
            }

            if (!$subject || $subject['name'] == 'MAIN' || file_exists(Yii::getAlias("@fileWrites/$files[0]"))) {
                foreach ($files as $fl)
                    unlink(Yii::getAlias("@fileWrites/$fl"));
            } else {
                $client = new Client();
                $response = $client->createRequest()
                    ->setMethod('post')
                    ->setUrl($subject['link'].'exams/delete-homework')
                    ->setData([
                        'file' => $files,
                        'appKey' => Yii::$app->params['secretKey'],
                    ])->send();
            }

            $model->check = 1;
            $model->check_at = time();
            $answers['user_exp'] += $exp;
            $answers['user_points'] += $points;
            if ($answers['user_points'] > $answers['max_points'])
                $answers['user_points'] = $answers['max_points'];

            $marks = json_decode($model->fullexam->marks, true);
            foreach ($marks as $mark => $range) {
                if ($answers['user_points'] >= $range[0] && $answers['user_points'] <= $range[1]) {
                    if ($examsStats['full_last']['id'] == $model->id) {
                        $examsStats['full_last']['points'] = $answers['user_points'];
                        $examsStats['full_last']['mark'] = $mark;
                    }
                    $answers['mark'] = $mark;
                }
            }

            $model->teacher_id = $user_id;
            $model->teacher_comment = $comment;
            $model->answers = json_encode($answers);
            $model->update();

            $user->statistics = json_encode($fullStats);
            $user->addExp($exp);
            $user->update();
            
            return $this->redirect(['index', 'type'=>'exam']);
        } // end $isPost

        return $this->render('exam', [
            'model' => $model,
            'subject' => $subject,
            'writes' => $writes,
            'answers' => $writesAns,
        ]);
    }
}