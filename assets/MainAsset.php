<?php

namespace app\assets;

use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/normalize.css',
        'css/icons.css',
        'css/base.css',
        // 'css/mainPage.css',
        'css/main.min.css',
        'css/vendor.css',
        // 'css/jquery.mCustomScrollbar.min.css',
    ];
    public $js = [
        // 'scripts/modernizr.js',
        // 'scripts/libs/jquery.min.js',
        // 'scripts/libs/jquery.cookie.js',
        // 'scripts/libs/jquery.mCustomScrollbar.concat.min.js',
        'scripts/plugins.js',
        'scripts/common.js',
    ];
    public $depends = [
    ];
}
