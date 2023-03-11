<?php

namespace app\assets;

use yii\web\AssetBundle;

class DocumentAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/document.min.css',
    ];
    public $js = [
        // 'scripts/libs/jquery.min.js',
    ];
    public $depends = [];
}
