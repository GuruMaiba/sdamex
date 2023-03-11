<?php

namespace app\modules\closedoor\controllers;

use Yii;
use app\models\{Level};
use app\controllers\AppController;

class LevelController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'index'             => ['GET'],
                    'delete'            => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                // 'except' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
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
            'levels' => Level::find()->asArray()->all(),
        ]);
    }

    public function actionChangeExp()
    {
        $lvl = $_POST['lvl'];
        $newExp = $_POST['newExp'];
        if (empty($lvl) || empty($newExp) || $lvl < -1)
            return 0;
        
        $prevLvl = Level::find()->where(['id'=>($lvl-1)])->asArray()->limit(1)->one();
        $lvl = Level::find()->where(['id'=>$lvl])->limit(1)->one();
        if (!$prevLvl || !$lvl || $newExp < $prevLvl['exp'])
            return 0;

        $lvl->exp = $newExp;
        $lvl->update();
        return 1;
    }

    public function actionChangeMax()
    {
        $lvl = $_POST['lvl'];
        if (empty($lvl) || $lvl < -1)
            return 0;
        
        $lvl = Level::find()->where(['id'=>$lvl])->limit(1)->one();
        if (!$lvl)
            return 0;

        Level::updateAll(['isMax'=>false]);
        $lvl->isMax = true;
        $lvl->update();
        return 1;
    }

    public function actionAdd()
    {
        $lvl = new Level;
        $lvl->save();
        return $this->renderPartial('_lvlRow', [
            'level' => [
                'id' => $lvl->id,
                'exp' => 0,
                'isMax' => false,
            ],
        ]);
    }
}
