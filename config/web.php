<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => 'SDAMEX',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru',
    'timeZone' => 'UTC',
    'aliases' => [
        '@bower'            => '@vendor/bower-asset',
        '@npm'              => '@vendor/npm-asset',
        '@home'             => '/',
        '@audioFolder'      => 'files/audio',
        '@fileOther'        => 'files/other',
        '@fileWrites'       => 'files/writes',
        '@imgFolder'        => 'css/images',
        '@imgUser'          => 'css/images/users',
        '@uAvaSmall'        => 'css/images/users/avatars/small',
        '@uAvaLarge'        => 'css/images/users/avatars/large',
        '@webnAvaSmall'     => 'css/images/webinars/small',
        '@webnAvaLarge'     => 'css/images/webinars/large',
        '@crsAvaSmall'      => 'css/images/courses/small',
        '@crsAvaLarge'      => 'css/images/courses/large',
        '@crsAvaModule'     => 'css/images/courses/modules',
        '@imgMem'           => 'css/images/mems',
        '@imgTeamp'         => 'css/images/teamps',
        '@imgOther'         => 'css/images/other',
        '@scrLibs'          => 'scripts/libs',
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'assetManager' => [
            'linkAssets' => false, // true
            'appendTimestamp' => YII_ENV_DEV ? false : true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,   // do not publish the bundle
                    'js' => [
                        'scripts/libs/jquery.min.js',
                        'scripts/libs/jquery.cookie.js',
                    ]
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '4c3b4426845aacbf8d472eb3936468eb',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'enableAutoLogin' => true,
            'identityClass' => 'app\models\User',
            'identityCookie' => [
                'name' => '_identity',
                // 'httpOnly' => true,
                'path' => '/',
                'domain' => YII_ENV_DEV ? '.sdamex.loc' : '.sdamex.ru',  
            ],
            'loginUrl' => ['account/login'],
        ],
        'session' => [
            'cookieParams' => [
                'domain' => YII_ENV_DEV ? '.sdamex.loc' : '.sdamex.ru',
            ],
            // 'name' => 'PHPMAINSESSID',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false, // false - send real emails
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.beget.com',
                'username' => 'norepeat@sdamex.ru',
                'password' => '%9RnXRKAx4N7&NEg', // ilovemywork2020
                'port' => '465', // 587
                'encryption' => 'ssl', // tls
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            // 'enableStrictParsing' => true, // правила которые уэе настроены
            // 'normalizer' => [
            //     'class' => 'yii\web\UrlNormalizer',
            //     // используем временный редирект вместо постоянного
            //     'action' => UrlNormalizer::ACTION_REDIRECT_TEMPORARY,
            // ],
            'rules' => [
                // [
                //     'pattern' => 'posts/<page:\d+>/<tag>',
                //     'route' => 'post/index',
                //     'defaults' => ['page' => 1, 'tag' => ''],
                // ],

                [
                    'pattern' => 'oauth-vk',
                    'route' => 'account/oauth-vk',
                ],
                [
                    'pattern' => 'oauth-fb',
                    'route' => 'account/oauth-fb',
                ],
                [
                    'pattern' => 'oauth-gl',
                    'route' => 'account/oauth-gl',
                ],
                [
                    'pattern' => 'terms',
                    'route' => 'site/terms',
                ],
                [
                    'pattern' => 'contract-offer',
                    'route' => 'site/contract-offer',
                ],

                // АДМИНКА
                '<module:closedoor>' => '<module>/default/index',

                // СТАНДАРТНЫЕ
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                    '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<id:\d+>' => '<module>/<controller>/index',
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                    '<controller:\w+>/<id:\d+>' => '<controller>/index',
                    '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<module:\w+>/<controller:\w+>' => '<module>/<controller>/index',
                // '<module:\w+>' => '<module>/default/index',
                    '<controller:\w+>' => '<controller>/index',
                '' => 'site/index'
            ],
        ],
    ],
    'modules' => [
        'closedoor' => [
            'class' => 'app\modules\closedoor\Module',
            'as access' => [ // if you need to set access
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['teacher', 'assistant', 'speaker', 'financier'],
                    ],
                ]
            ],
        ],
        // 'personal' => [
        //     'class' => 'app\modules\personal\Module',
        // ],
        'rbac' => [
            'class' => 'mdm\admin\Module',
            'controllerMap' => [
                 'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    /* 'userClassName' => 'app\models\User', */
                    'idField' => 'id',
                    'usernameField' => 'username',
                ],
            ],
            'layout' => 'left-menu',
            'mainLayout' => '@app/views/layouts/admin.php',
        ],
    ],
    'params' => $params,
];

if (YII_DEBUG) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        // 'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
