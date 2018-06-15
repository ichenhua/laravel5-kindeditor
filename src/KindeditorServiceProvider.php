<?php
namespace Chenhua\Kindeditor;

/**
 * Created by PhpStorm.
 * Author: ChenHua <http://www.ichenhua.cn>
 * Date: 2018/6/14 19:04
 */

use Illuminate\Support\ServiceProvider;

class KindeditorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \Route::post('kindeditor/upload',function (){
            $info = Kindeditor::upload();
            return json_encode($info);
        });

        $this->loadViewsFrom('resources/views/vendor/kindeditor', 'kindeditor');

        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/kindeditor'),
            __DIR__.'/views' => base_path('resources/views/vendor/kindeditor'),
            __DIR__.'/config/kindeditor.php' => base_path('config/kindeditor.php'),
        ], 'kindeditor');
    }

    public function register()
    {
        //
    }
}
