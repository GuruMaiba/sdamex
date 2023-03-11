<?php

namespace app\modules\closedoor\controllers;

use Yii;
use app\models\Theme;
use app\models\exam\{Fullexam, Section, Exercise};
use app\controllers\AppController;
use yii\web\NotFoundHttpException;
// use yii\filters\VerbFilter;

class ThemeController extends AppController
{
    // public function behaviors()
    // {
    //     return [
    //         'verbs' => [
    //             'class' => VerbFilter::className(),
    //             'actions' => [
    //                 // 'create' => ['POST'],
    //             ],
    //         ],
    //     ];
    // }

    public function actionIndex()
    {
        return $this->render('index', [
            'model' => Theme::find()->asArray()->all(),
        ]);
    }

    public function actionAdd()
    {
        $name = $_POST['name'];
        if ($name == '' || Theme::find()->where(['name'=>$name])->limit(1)->exists())
            return 0;

        $theme = new Theme;
        $theme->name = $name;
        $theme->save();

        return $this->renderPartial('_item', ['model'=>[$theme]]);
    }

    public function actionDel()
    {
        $ids = $_POST['ids'];
        if (!$ids)
            return 0;

        Theme::deleteAll(['in', 'id', $ids]);
        return 1;
    }
}
