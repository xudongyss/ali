<?php
namespace xudongyss\ali\oss;

use xudongyss\ali\base\AliCloudBase;
use OSS\OssClient;
use OSS\Core\OssException;

class AliCloudOss extends AliCloudBase{
    /**
     * @var OssClient
     */
    static $client;
    
    public static function client($accessKeyId, $accessKeySecret, $endpoint, $securityToken = null) {
        if(static::$client) return static::$client;
        
        static::$client = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false, $securityToken);
    }
    
    /**
     * 文件上传
     */
    public static function uploadFile($bucket, $object, $file) {
        try {
            $result = static::$client->uploadFile($bucket, $object, $file);
            
            return $result;
        }catch(OssException $e) {
            echo $e->getMessage();
            static::setErrorMessage($e->getMessage());
            
            return false;
        }
        
    }
}