<?php

namespace app\modules\closedoor\controllers;

use Yii;
use app\models\course\Course;
use app\models\exam\{Fullexam, Section, Exercise};
use app\models\exam\test\Test;
use app\models\exam\write\Write;
use app\models\exam\correlate\Correlate;
use app\models\exam\addition\Addition;
use app\controllers\AppController;
use app\components\ExamType;
use yii\web\NotFoundHttpException;

class FullexamController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'create-section' => ['POST'],
                    'update-section' => ['POST'],
                    'delete-section' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                // 'except' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['assistant', 'moderator'],
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
        // return $this->debug();
        return $this->render('index', [
            'model' => Fullexam::find()->asArray()->all(),
        ]);
    }

    public function actionCreate($id=0)
    {
        $model = new Fullexam();
        $id = ($_POST['Fullexam']['id'] > 0) ? $_POST['Fullexam']['id'] : $id;
        if ($id > 0) {
            $model = Fullexam::find()->where(['id'=>$id])
                ->with([
                    'sections' => function ($query) {
                        $query->asArray();
                    },
                    'sections.exercises' => function ($query) {
                        $query->asArray();
                    }
                ])->limit(1)->one();
            if ($model == null)
                throw new NotFoundHttpException('Страница не найдена!');
        }

        if ($model->load(Yii::$app->request->post()) && isset($_POST['Marks'])) {
            $marks = [];
            foreach ($_POST['Marks'] as $mark => $val)
                $marks[$mark] = [$val['min'],$val['max']];
            $model->marks = json_encode($marks);
            
            if ($model->validate())
                $model->save();

            return $this->redirect(['create', 'id'=>$model->id]);
        }

        foreach (Yii::$app->params['listSubs'] as $sId => $sVal) {
            if ($sVal['isActive'])
                $model->subjects[$sId] = $sVal['name'];
        }

        foreach (Course::find()->select(['id','subject_id','title'])->asArray()->all() as $cVal) {
            $model->courses[$cVal['id']] = $cVal['title'].' ('.Yii::$app->params['listSubs'][$cVal['subject_id']]['lable'].')';
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionDeleteFullexam($id)
    {
        if (empty($id) || $id < 1 || !Yii::$app->user->can('admin'))
            throw new NotFoundHttpException('Страница не найдена!');
        
        $section = Fullexam::findOne($id);
        $section->delete();
        return $this->redirect('index');
    }

    public function actionCreateSection()
    {
        $fullexam = $_POST['id'];
        if (empty($fullexam) || $fullexam < 1 || !Fullexam::find()->where(['id'=>$fullexam])->limit(1)->exists())
            return 0;

        $section = new Section();
        $section->fullexam_id = $fullexam;
        $section->place = 1;
        $section->name = 'Новая секция';
        $section->save();

        // return $this->debug();
        return $this->renderPartial('_section', [
            'model' => [$section],
        ]);
    }

    public function actionUpdateSection()
    {
        $id = $_POST['id'];
        if (empty($id) || $id < 1)
            return 0;
        
        $section = Section::findOne($id);
        if (!$section)
            return 0;

        $section->place = $_POST['place'];
        $section->name = $_POST['name'];
        $section->publish = $_POST['publish'];
        $oldPublish = $section->getOldAttribute('publish');

        if (!$section->validate())
            return 0;

        if ($section->publish != $oldPublish) {
            $exercises = Exercise::find()->select('fullexam_points')->where(['section_id'=>$id])->asArray()->all();
            if ($exercises) {
                $fullexam = $section->fullexam;
                if (!$fullexam)
                    return 0;
                
                foreach ($exercises as $exe) {
                    if ($section->publish && !$oldPublish)
                        $fullexam->max_points += $exe['fullexam_points'];
                    else if (!$section->publish && $oldPublish)
                        $fullexam->max_points -= $exe['fullexam_points'];
                } // foreach $exercises

                $marks = json_decode($fullexam->marks, true);
                $marks[5][1] = $fullexam->max_points;
                $fullexam->marks = json_encode($marks);

                $fullexam->update();
            } // if $exercises
        } // if publish
        
        $section->update();
        return 1;
    }

    public function actionDeleteSection()
    {
        $id = $_POST['id'];
        if (empty($id) || $id < 1 || !Yii::$app->user->can('admin'))
            return 0;
        
        $section = Section::findOne($id);
        $section->delete();
        return 1;
    }

    public function actionExercise($id=0, $section_id=0)
    {
        $model = new Exercise();
        $model->section_id = $section_id;
        $model->place = 1;
        $id = ($_POST['Exercise']['id'] > 0) ? $_POST['Exercise']['id'] : $id;

        if ($id <= 0 && $section_id == 0)
            throw new NotFoundHttpException('Страница не найдена!');

        if ($id > 0) {
            $model = Exercise::find()->where(['id'=>$id])->with(['section','section.fullexam'])->limit(1)->one();
            $section = $model->section;
        } else
            $section = Section::find()->where(['id'=>$section_id])->with(['fullexam'])->limit(1)->one();

        $fullexam = $section->fullexam;
        if (!$model || !$section || !$fullexam)
            throw new NotFoundHttpException('Страница не найдена!');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($section->publish) {
                $isChange = false;
                $isPublish = $model->getOldAttribute('publish');
                $isFullexam = $model->getOldAttribute('fullexam');
                $oldPoints = $model->getOldAttribute('fullexam_points');
    
                // Если изменили количество баллов
                if ($isFullexam && $isPublish && $model->fullexam && $model->publish && $model->fullexam_points != $oldPoints) {
                    $fullexam->max_points += $model->fullexam_points - $oldPoints; $isChange = true;
                // Если опубликовали и включили в полный экзамен одновременно
                } else if ((!$isFullexam || !$isPublish) && $model->fullexam && $model->publish) {
                    $fullexam->max_points += $model->fullexam_points; $isChange = true;
                // Если сняли с публикации или исключили из полного экзамена
                } else if ($isFullexam && $isPublish && (!$model->fullexam || !$model->publish)) {
                    $fullexam->max_points -= $oldPoints; $isChange = true;
                }
    
                if ($isChange) {
                    $marks = json_decode($fullexam->marks, true);
                    $marks[5][1] = $fullexam->max_points;
                    $fullexam->marks = json_encode($marks);
                }
    
                $fullexam->update();
            }
            
            $model->save();

            return $this->redirect(['fullexam/exercise', 'id'=>$model->id]);
        }

        return $this->render('exercise', [
            'model' => $model,
            'fullexam_id' => $fullexam->id,
        ]);
    }

    public function actionDeleteExercise()
    {
        $id = (Yii::$app->request->isPost) ? $_POST['id'] : $_GET['id'];
        if (empty($id) || $id < 1 || !Yii::$app->user->can('admin'))
            return 0;

        $exe = Exercise::find()->where(['id'=>$id])->limit(1)->one();
        if ($exe == null) {
            if (Yii::$app->request->isPost) 
                return 0;
            else
                throw new NotFoundHttpException('Страница не найдена!');
        }

        if ($exe->fullexam && $exe->publish && $exe->section && $exe->section->fullexam) {
            $exe->section->fullexam->max_points -= $exe->fullexam_points;
            $exe->section->fullexam->update();
        }

        $exe->delete();
        return (Yii::$app->request->isPost) ? 1 : $this->redirect('create/'.$exe->section_id);
    }

    public function actionDeleteTask()
    {
        $isPost = Yii::$app->request->isPost;
        $req = ($isPost) ? $_POST : $_GET;
        $id = (int)$req['id'];
        $type = (int)$req['type'];
        if (empty($id) || $id < 1 || empty($type) || !Yii::$app->user->can('admin'))
            return 0;

        $model = null;
        if ($type == ExamType::TEST)
            $model = Test::findOne($id);
        else if ($type == ExamType::WRITE)
            $model = Write::findOne($id);
        else if ($type == ExamType::CORRELATE)
            $model = Correlate::findOne($id);
        else if ($type == ExamType::ADDITION)
            $model = Addition::findOne($id);

        if ($model) {
            $model->delete();
            return ($isPost) ? 1 : $this->redirect('exercise/'.$model->exercise_id);
        } else {
            if ($isPost)
                return 0;
            else
                throw new NotFoundHttpException('Страница не найдена!');
        }
    }
}
