<?php

namespace app\modules\closedoor\controllers;

use Yii;
use app\models\{User, BanUser};
use app\models\search\BanSearch;
use app\components\UserStatus;
use app\controllers\AppController;
use yii\web\NotFoundHttpException;

/**
 * BanController implements the CRUD actions for BanUser model.
 */
class BanController extends AppController
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

    /** // DEBUG: ?
     * Lists all BanUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BanSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BanUser model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BanUser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = 0)
    {
        if ($id == 0)
            throw new NotFoundHttpException('Не указан пользователь!');

        $user = User::findOne($id);

        if (!$user)
            throw new NotFoundHttpException('Такого пользователя не существует!');

        $model = $user->getBan();

        if ($model->load($_POST)) {
            $user->status = $model->status;
            $model->ban_begin = time();

            if ($model->status != UserStatus::PERMANENT_BAN) {
                if (isset($model->timeBan))
                    $model->ban_end = strtotime('+ ' . $model->timeBan);
            } else {
                $model->ban_end = time();
            }

            $model->save();
            $user->update();
            return $this->redirect(['view', 'id' => $model->user_id]);
        }

        $model->user_id = $id;
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BanUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAddBan($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BanUser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $user = User::findOne($id);
        $ban = $user->getBan();
        if ($ban) {
            $user->status = UserStatus::ACTIVE;
            $ban->ban_end = $ban->ban_begin;
            $ban->cause = null;
            $user->update();
            $ban->update();
            return $this->redirect(['view', 'id' => $ban->user_id]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the BanUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BanUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BanUser::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
