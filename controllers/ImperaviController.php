<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\helpers\Inflector;
use yii\imagine\Image;
use Imagine\Image\Box;

class ImperaviController extends AppController {

    public $enableCsrfValidation = false;

    public function actionImagesGet() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $options = ['only' => ['*.jpg', '*.jpeg', '*.png', '*.gif']];
        $files = [];

        foreach (FileHelper::findFiles('css/images/other/', $options) as $path) {
            $file = basename($path);
            $url = Yii::$app->params['listSubs'][1]['link'].Url::to('@imgOther/'.urlencode($file));

            $files[] = [
                'id' => $file,
                'title' => $file,
                'thumb' => $url,
                'image' => $url,
            ];
        }

        return $files;
    }

    public function actionImageUpload() {
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $savePath = 'css/images/other/';
            $file = UploadedFile::getInstanceByName('file');
            $model = new DynamicModel(['image' => $file]);
            $model->addRule('image', 'image', ['extensions' => 'png, jpg, jpeg', 'maxSize' => 10*1024*1024])->validate();

            if ($model->image === null || $model->hasErrors()) {
                return ['error'=>'Картинка отсутствует!'];               
            }

            $name = Inflector::slug($model->image->baseName) . '.' . $model->image->extension;
            $path = Yii::$app->params['teampsPath'].$name;
    
            $model->image->saveAs($path);
    
            while (file_exists($savePath.$name)) {
                $name = uniqid() . '.' . $model->image->extension;
            }
    
            Image::getImagine()->open($path)
                    ->thumbnail(new Box(1300,500))
                    ->save(Yii::getAlias('@webroot/'.$savePath.$name), ['quality' => 70]);
            unlink($path);

            return ['id' => 'img-'.$name, 'filelink' => Yii::$app->params['listSubs'][1]['link'].Url::to(['@imgOther/'.$name])]; //'filename' => 'lol'
        }
    }

    public function actionImageDelete() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isDelete && Yii::$app->request->isAjax) {
            $fileName = Yii::$app->request->post('fileName', null);

            if ($fileName === null)
                return ['error' => 'Отсутствует имя файла!'];

            $file = 'css/images/other/' . $fileName;

            if (file_exists($file) && unlink($file))
                return ['url' => Yii::$app->params['listSubs'][1]['link'].Url::to(['@imgOther/'.urlencode($fileName)])];      
        }

        return ['error' => 'Ошибка удаления!'];
    }
    
}
