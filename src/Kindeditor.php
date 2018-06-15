<?php
namespace Chenhua\Kindeditor;

/**
 * Created by PhpStorm.
 * Author: ChenHua <http://www.ichenhua.cn>
 * Date: 2018/6/14 20:06
 */

use Illuminate\Support\Facades\Request;
use OSS\OssClient;
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;

class Kindeditor
{
    static $_errors = array();
    static $_url = array();
    static $real_path;

    protected static function addError($message)
    {
        if (!empty($message)) {
            self::$_errors[] = $message;
        }
    }

    protected static function getLastError()
    {
        return empty(self::$_errors) ? '' : array_pop(self::$_errors);
    }

    //文件上传
    public static function upload()
    {
        try {
            if (Request::hasFile('imgFile')) {
                $pic = Request::file('imgFile');
                if ($pic->isValid()) {
                    $path    = config('kindeditor.connections.local.prefix', 'uploads');
                    $newName = date('Ymd-His') . '-' . rand(100, 999) . '.' . $pic->getClientOriginalExtension();
                    //本地保存
                    $pic->move($path, $newName);
                    self::$_url['local'] = asset($path . '/' . $newName);
                    //本地保存绝对路径
                    self::$real_path = $path . '/' . $newName;
                    //同步到七牛
                    if (in_array('qiniu', config('kindeditor.dirver', []))) {
                        self::_qiniu();
                    }
                    //同步到阿里云
                    if (in_array('aliyun', config('kindeditor.dirver', []))) {
                        self::_aliyun();
                    }
                } else {
                    self::addError('The file is invalid');
                }
            } else {
                self::addError('Not File');
            }
        } catch (\Exception $e) {
            self::addError($e->getMessage());
        }

        $data = array(
            'error' => empty(self::getLastError()) ? 0 : 1,
            'message' => self::getLastError() ?: 'success',
            'url'     => isset(self::$_url[config('kindeditor.default')])
                        && empty(self::getLastError()) ? self::$_url[config('kindeditor.default')] : ''
        );
        return $data;
    }

    //上传到七牛云
    private static function _qiniu()
    {
        try{
            $qiniu = config('kindeditor.connections.qiniu');
            if(!$qiniu) throw new \Exception('config kindeditor.qiniu exception.');
            //参数设置
            $accessKey = $qiniu['access_key'];
            $secretKey = $qiniu['secret_key'];
            $bucketName = $qiniu['bucket'];
            $domain = $qiniu['domain'];
            //上传七牛后的文件名
            $file_name = $qiniu['prefix'].basename(self::$_url['local']);
            $upManager = new UploadManager();
            $auth = new Auth($accessKey, $secretKey);
            $token = $auth->uploadToken($bucketName);
            list($ret, $error) = $upManager->putFile($token, $file_name, self::$real_path);
            if($error){
                throw new \Exception('qiniu upload fail.');
            }else{
                self::$_url['qiniu'] = $domain .'/'. $ret['key'];
            }
        } catch (\Exception $e) {
            self::addError($e->getMessage());
        }
    }

    //上传到阿里云
    private static function _aliyun()
    {
        try{
            $aliyun = config('kindeditor.connections.aliyun');
            if(!$aliyun) throw new \Exception('config kindeditor.aliyun exception.');
            //通过配置项初始化oss设置
            $accessKeyId = $aliyun['ak_id'];
            $accessKeySecret = $aliyun['ak_secret'];
            $endpoint = $aliyun['end_point'];
            $bucket = $aliyun['bucket'];
            //上传阿里云后的文件名
            $object = $aliyun['prefix'].basename(self::$_url['local']);
            //实例化阿里云处理类
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $result = $ossClient->uploadFile($bucket, $object, self::$real_path);
            if(isset($result['info']['url'])){
                self::$_url['aliyun'] = $result['info']['url'];
            }else{
                throw new \Exception('aliyun upload fail.');
            }
        } catch (\Exception $e) {
            self::addError($e->getMessage());
        }
    }

}