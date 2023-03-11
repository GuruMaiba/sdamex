<?php

namespace app\modules\closedoor\controllers;
use Yii;

use app\controllers\AppController;
use app\models\Theme;
use app\models\course\Lesson;
use app\models\webinar\Webinar;
use app\models\exam\{Fullexam, Section, Exercise};
use app\models\exam\test\{Test, Question, Answer};
use app\models\exam\correlate\{Correlate, Pair};
use app\models\exam\addition\Addition;
use app\models\exam\write\Write;
use yii\web\NotFoundHttpException;
// use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\helpers\Html;

class ExamController extends AppController
{
    public function behaviors()
    {
        return [
            // 'verbs' => [
            //     'class' => \yii\filters\VerbFilter::className(),
            //     'actions' => [
            //         'create-section' => ['POST'],
            //         'update-section' => ['POST'],
            //         'delete-section' => ['POST'],
            //     ],
            // ],
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

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionTest($id=0, $exercise_id=0, $webinar_id=0, $lesson_id=0) {
        $model = new Test();
        $isLang = Yii::$app->params['isLang'];
        $link = [
            'model' => null,
            'type' => null
        ];
        $id = ($_POST['Test']['id'] > 0) ? $_POST['Test']['id'] : $id;
        $ids = [
            'course'    => $_GET['course_id'],
            'module'    => $_GET['module_id'],
            'lesson'    => $_GET['lesson_id'],
            'webinar'   => $_GET['webinar_id'],
            'exercise'  => $_GET['exercise_id'],
            'fullexam'  => $_GET['fullexam_id'],
        ];

        if ($id < 1) {
            if ($exercise_id <= 0 && $lesson_id <= 0 && $webinar_id <= 0)
                throw new NotFoundHttpException('Отсутствует обязательный парамметр!');

            if ($exercise_id > 0) {
                $link['model'] = Exercise::find()->where(['id'=>$exercise_id])->limit(1)->one();
                $link['type'] = 'exe';
                $model->exercise_id = ($link['model'] != null) ? $exercise_id : 0;
            } else if ($lesson_id > 0) {
                $link['model'] = Lesson::find()->where(['id'=>$lesson_id])->limit(1)->one();
                $link['type'] = 'les';
                if ($link['model']->examtest_id > 0)
                    throw new NotFoundHttpException('К этому уроку уже привязан тест!');
                $model->lesson_id = ($link['model'] != null) ? $lesson_id : 0;
            } else if ($webinar_id > 0) {
                $link['model'] = Webinar::find()->where(['id'=>$webinar_id])->limit(1)->one();
                $link['type'] = 'web';
                if ($link['model']->examtest_id > 0)
                    throw new NotFoundHttpException('К этому вебинару уже привязан тест!');
                $model->webinar_id = ($link['model'] != null) ? $webinar_id : 0;
            }

            if ($model->exercise_id == 0 && $model->lesson_id == 0 && $model->webinar_id == 0)
                throw new NotFoundHttpException('Отсутствует модель, к который вы пытаетесь привязать тестовое задание!');
        } else {
            $model = Test::find()->where(['id'=>$id])->with(['questions','questions.answers'])->limit(1)->one();
            if ($model == null)
                throw new NotFoundHttpException('Страница не найдена!');
        }

        if ($model->load($_POST)) {
            if ($isLang) {
                $model->task = $this->addTagTranslate($model->task);
                $model->text = $this->addTagTranslate($model->text);
            }

            $model->track = UploadedFile::getInstance($model,'track');
            if ($model->track != null) {
                // устанавливаем путь сохранения
                $model->savePath = Yii::getAlias('@audioFolder/');
                // загружаем фаил и устанавливаем имя
                $model->audio_name = $model->fileUpload($model->track, $model->audio_name);
                $model->track = null;
            }

            $model->save();

            // Получаем массив с вопросами
            $newQuestions = $_POST['Test']['questions'];
            // Создаём массив с правильными ответами для json строки
            $correctAnswers = [];
            // Получаем вопросы
            $oldQuestions = $model->questions;
            // Перебираем вопросы
            $place = 1;

            foreach ((array)$newQuestions as $qst) {
                $newQst = new Question(); // Переменная вопроса

                // Если это уже созданный вопрос
                if ($qst['id'] > 0) {
                    // находим у модели нужный нам вопрос
                    foreach ($oldQuestions as $question) {
                        if ($qst['id'] == $question->id) {
                            $newQst = $question; // забиваем в переменную
                            break;
                        }
                    }
                    // удаляем из массива, чтобы сократить время будущих переборов
                    unset($oldQuestions[array_search($newQst, $oldQuestions)]);
                }

                $qst['text'] = ($isLang) ? $this->addTagTranslate($qst['text']) : $qst['text'];
                $attr = [
                    'examtest_id' => $model->id,                    // добавляем вопрос к тесту
                    'text' => $qst['text'],                         // текст
                    'multiple_answer' => $qst['multiple_answer'],   // множественный ответ
                    'hard' => $qst['hard'],                         // сложный вопрос х2 опыта
                    'place' => $place,                              // место
                ];
                ++$place;

                // Устанавливаем аттрибуты
                $newQst->attributes = $attr;
                // Сохраняем вопрос
                // return $this->debug($newQst);
                $newQst->save();
                // создаём ячейку для ответов на текущий вопрос
                $correctAnswers[$newQst->id] = [
                    'themes' => json_decode($qst['themes']),
                    'answers' => [],
                ];

                // Получаем ответы
                $answers = [];
                if ($qst['id'] > 0)
                    $answers = $newQst->answers;

                // Повторяем тоже самое с ответами
                foreach ((array)$qst['answers'] as $ans) {
                    $newAns = new Answer();

                    if ($ans['id'] > 0) {
                        foreach ($answers as $answer) {
                            if ($ans['id'] == $answer->id) {
                                $newAns = $answer;
                                break;
                            }
                        }
                        unset($answers[array_search($newAns, $answers)]);
                    }

                    $ans['text'] = ($isLang) ? $this->addTagTranslate($ans['text']) : $ans['text'];
                    $attr = [
                        'question_id' => $newQst->id,                       // добавляем вопрос к ответу
                        'text' => $ans['text'],                             // текст
                    ];

                    $newAns->attributes = $attr;
                    $newAns->save();

                    if ($ans['correct'])
                        $correctAnswers[$newQst->id]['answers'][] = $newAns->id;
                } // end foreach

                // Если в массиве остались ответы, то мы их удаляем
                foreach ((array)$answers as $answer)
                    $answer->delete();
            } // end foreach

            // удаляем оставшиеся вопросы
            foreach ((array)$oldQuestions as $question)
                $question->delete();

            $model->correct_answers = json_encode($correctAnswers);
            $model->update();

            if ($id < 1) {
                if ($exercise_id > 0) {
                    $link['model']->task_count++;
                    $link['model']->update();
                } else if ($lesson_id > 0 || $webinar_id > 0) {
                    $link['model']->examtest_id = $model->id;
                    $link['model']->update();
                }
            }

            return $this->redirect(['test',
                'id'           => $model->id,
                'course_id'    => $ids['course'],
                'module_id'    => $ids['module'],
                'lesson_id'    => $ids['lesson'],
                'webinar_id'   => $ids['webinar'],
                'exercise_id'  => $ids['exercise'],
                'fullexam_id'  => $ids['fullexam'],
                ]);
        }

        if ($isLang) {
            $model->task = $this->delTagTranslate($model->task);
            $model->text = $this->delTagTranslate($model->text);
            foreach ($model->questions as $qst) {
                $qst->text = $this->delTagTranslate($qst->text);
                foreach($qst->answers as $ans) {
                    $ans->text = $this->delTagTranslate($ans->text);
                }
            }
        }

        return $this->render('test', [
            'model' => $model,
            'themes' => Theme::find()->asArray()->all(),
            'ids' => $ids,
        ]);
    }

    public function actionWrite($id=0, $exercise_id=0, $webinar_id=0, $lesson_id=0) {
        $model = new Write();
        $isLang = Yii::$app->params['isLang'];
        $link = [
            'model' => null,
            'type' => null
        ];
        $id = ($_POST['Write']['id'] > 0) ? $_POST['Write']['id'] : $id;
        $ids = [
            'course'    => $_GET['course_id'],
            'module'    => $_GET['module_id'],
            'lesson'    => $_GET['lesson_id'],
            'webinar'   => $_GET['webinar_id'],
            'exercise'  => $_GET['exercise_id'],
            'fullexam'  => $_GET['fullexam_id'],
        ];

        if ($id < 1) {
            if ($exercise_id <= 0 && $lesson_id <= 0 && $webinar_id <= 0)
                throw new NotFoundHttpException('Отсутствует обязательный парамметр!');

            if ($exercise_id > 0) {
                $link['model'] = Exercise::find()->where(['id'=>$exercise_id])->limit(1)->one();
                $link['type'] = 'exe';
                $model->exercise_id = ($link['model'] != null) ? $exercise_id : 0;
            } else if ($lesson_id > 0) {
                $link['model'] = Lesson::find()->where(['id'=>$lesson_id])->limit(1)->one();
                $link['type'] = 'les';
                if ($link['model']->examwrite_id > 0)
                    throw new NotFoundHttpException('К этому уроку уже привязан тест!');
                $model->lesson_id = ($link['model'] != null) ? $lesson_id : 0;
            } else if ($webinar_id > 0) {
                $link['model'] = Webinar::find()->where(['id'=>$webinar_id])->limit(1)->one();
                $link['type'] = 'web';
                if ($link['model']->examwrite_id > 0)
                    throw new NotFoundHttpException('К этому вебинару уже привязан тест!');
                $model->webinar_id = ($link['model'] != null) ? $webinar_id : 0;
            }

            if ($model->exercise_id == 0 && $model->lesson_id == 0 && $model->webinar_id == 0)
                throw new NotFoundHttpException('Отсутствует модель, к который вы пытаетесь привязать тестовое задание!');
        } else {
            $model = Write::find()->where(['id'=>$id])->limit(1)->one();
            if ($model == null)
                throw new NotFoundHttpException('Страница не найдена!');
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($isLang) {
                $model->task = $this->addTagTranslate($model->task);
                $model->text = $this->addTagTranslate($model->text);
            }

            $model->track = UploadedFile::getInstance($model,'track');
            if ($model->track != null) {
                // устанавливаем путь сохранения
                $model->savePath = Yii::getAlias('@audioFolder/');
                // загружаем фаил и устанавливаем имя
                $model->audio_name = $model->fileUpload($model->track, $model->audio_name);
                $model->track = null;
            }

            $model->save();

            if ($id < 1) {
                if ($exercise_id > 0) {
                    $link['model']->task_count++;
                    $link['model']->update();
                } else if ($lesson_id > 0 || $webinar_id > 0) {
                    $link['model']->examwrite_id = $model->id;
                    $link['model']->update();
                }
            }

            return $this->redirect(['write',
                'id'           => $model->id,
                'course_id'    => $ids['course'],
                'module_id'    => $ids['module'],
                'lesson_id'    => $ids['lesson'],
                'webinar_id'   => $ids['webinar'],
                'exercise_id'  => $ids['exercise'],
                'fullexam_id'  => $ids['fullexam'],
                ]);
        }

        if ($isLang) {
            $model->task = $this->delTagTranslate($model->task);
            $model->text = $this->delTagTranslate($model->text);
        }

        if ($model->themes == null)
            $model->themes = json_encode([]);

        return $this->render('write', [
            'model' => $model,
            'themes' => Theme::find()->asArray()->all(),
            'ids' => $ids,
        ]);
    }

    public function actionCorrelate($id=0, $exercise_id=0) {
        $model = new Correlate();
        $model->exercise_id = $exercise_id;
        $isLang = Yii::$app->params['isLang'];
        $id = ($_POST['Correlate']['id'] > 0) ? $_POST['Correlate']['id'] : $id;
        $exercise = null;

        if ($id > 0) {
            $model = Correlate::find()->where(['id'=>$id])->with(['pairs', 'exercise'])->limit(1)->one();
            $exercise = $model->exercise;
            if ($model == null)
                throw new NotFoundHttpException('Страница не найдена!');
        } else {
            if ($exercise_id > 0)
                $exercise = Exercise::find()->where(['id'=>$exercise_id])->limit(1)->one();
        }

        if ($exercise == null)
            throw new NotFoundHttpException('Нет привязки к заданию!');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($isLang) {
                $model->task = $this->addTagTranslate($model->task);
                $model->text = $this->addTagTranslate($model->text);
            }

            $model->track = UploadedFile::getInstance($model,'track');
            if ($model->track != null) {
                // устанавливаем путь сохранения
                $model->savePath = Yii::getAlias('@audioFolder/');
                // загружаем фаил и устанавливаем имя
                $model->audio_name = $model->fileUpload($model->track, $model->audio_name);
                $model->track = null;
            }
            $model->save();
            
            $newPairs = $_POST['Pairs'];
            $oldPairs = $model->pairs;
            $themes = [];

            foreach ((array)$newPairs as $new) {
                $pair = new Pair();

                if ($new['id'] > 0) {
                    foreach ((array)$oldPairs as $old) {
                        if ($new['id'] == $old->id) {
                            $pair = $old;
                            break;
                        }
                    }
                    if ($pair->id > 0)
                        // удаляем из массива, чтобы сократить время будущих переборов
                        unset($oldPairs[array_search($pair, $oldPairs)]);
                }

                $attr = [
                    'examcorrelate_id'  => $model->id,                                                              // Добавляем тест к соответствию
                    'qst_text'          => ($isLang) ? $this->addTagTranslate($new['qst_text']) : $new['qst_text'],  // Текст вопроса
                    'ans_text'          => ($isLang) ? $this->addTagTranslate($new['ans_text']) : $new['ans_text'],  // Текст ответа
                ];
                $pair->attributes = $attr;
                $pair->save();
                $themes[$pair->id] = json_decode($new['themes']);
            }

            // удаляем оставшиеся соответствия
            foreach ((array)$oldPairs as $pair)
                $pair->delete();

            $model->themes = json_encode($themes);
            $model->update();

            if ($exercise_id > 0) {
                $exercise->task_count++;
                $exercise->update();
            }

            return $this->redirect(['correlate', 'id'=>$model->id]);
        }

        if ($isLang) {
            $model->task = $this->delTagTranslate($model->task);
            $model->text = $this->delTagTranslate($model->text);
            foreach ($model->pairs as $pair) {
                $pair->qst_text = $this->delTagTranslate($pair->qst_text);
                $pair->ans_text = $this->delTagTranslate($pair->ans_text);
            }
        }
        
        return $this->render('correlate',[
            'model' => $model,
            'themes' => Theme::find()->asArray()->all(),
        ]);
    }

    public function actionAddition($id=0, $exercise_id=0) {
        $model = new Addition();
        $model->exercise_id = $exercise_id;
        $isLang = Yii::$app->params['isLang'];
        $id = ($_POST['Addition']['id'] > 0) ? $_POST['Addition']['id'] : $id;
        $exercise = null;

        if ($id > 0) {
            $model = Addition::find()->where(['id'=>$id])->with(['exercise'])->limit(1)->one();
            $exercise = $model->exercise;
            if ($model == null)
                throw new NotFoundHttpException('Страница не найдена!');
        } else {
            if ($exercise_id > 0)
                $exercise = Exercise::find()->where(['id'=>$exercise_id])->limit(1)->one();
        }

        if ($exercise == null)
            throw new NotFoundHttpException('Нет привязки к заданию!');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $pattern = '/\_+\((.*?)\)/';
            preg_match_all($pattern,$model->text,$match);

            if ($isLang) {
                // Вырезаем пропуски, чтобы в них не залетели span`ы
                $model->text = preg_replace_callback(
                    $pattern,
                    function ($matches) {
                        return '&$$$$$;';
                    },
                    $model->text
                );

                // Оборачиваем в span каждое слово
                $model->task = $this->addTagTranslate($model->task);
                $model->text = $this->addTagTranslate($model->text);

                // Возвращаем пропуски на свои места
                $i = -1;
                $model->text = preg_replace_callback(
                    '/\&\$\$\$\$\$;/',
                    function ($matches) use (&$i, $match) {
                        return $match[0][++$i];
                    },
                    $model->text
                );
            }

            $model->save();
            if ($exercise_id > 0) {
                $exercise->task_count++;
                $exercise->update();
            }

            return $this->redirect(['addition', 'id'=>$model->id]);
        }

        if ($isLang) {
            $model->task = $this->delTagTranslate($model->task);
            $model->text = $this->delTagTranslate($model->text);
        }
        
        return $this->render('addition',[
            'model' => $model,
            'themes' => Theme::find()->asArray()->all(),
        ]);
    }

    // public function getWords($match) {
    //     $words = [];
    //     $i = 0;
    //     foreach ((array)$match as $value) {
    //         $word = explode('/', $match[$i]);
    //         $words[$word[0]] = $word[1];
    //         ++$i;
    //     }
    //     return $words;
    // }
}
