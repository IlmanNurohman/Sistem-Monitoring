<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/plugins.min.css',
        'css/kaiadmin.min.css',
        'css/kaiadmin.css',
    ];
    public $js = [
        'js/core/popper.min.js',
        'js/plugin/jquery-scrollbar/jquery.scrollbar.min.js',
        'js/plugin/datatables/datatables.min.js',
        
        'js/kaiadmin.min.js',
         'https://cdn.jsdelivr.net/npm/sweetalert2@11',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}