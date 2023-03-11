<?php

namespace app\modules\closedoor;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin', 'teacher', 'assistant', 'speaker', 'financier'],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\closedoor\controllers';
    public $layout = '/admin';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
