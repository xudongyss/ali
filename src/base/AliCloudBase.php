<?php
namespace xudongyss\ali\base;

use AlibabaCloud\Client\AlibabaCloud;

class AliCloudBase{
    public static $accessKeyId = '';
    public static $accessSecret = '';
    public static $regionId = '';
    
    protected static $errorMessage = '';
    
    public static function init($accessKeyId, $accessSecret, $regionId = 'cn-hangzhou') {
        static::$accessKeyId = $accessKeyId;
        static::$accessSecret = $accessSecret;
        static::$regionId = $regionId;
        
        static::client($accessKeyId, $accessSecret, $regionId);
    }
    
    public static function getErrorMessage() {
        return static::$errorMessage;
    }
    
    protected static function setErrorMessage($errorMessage) {
        static::$errorMessage = $errorMessage;
    }
    
    public static function client($accessKeyId, $accessSecret, $regionId) {
        static $client;
        if($client) return ;
        
        AlibabaCloud::accessKeyClient($accessKeyId, $accessSecret)
                    ->regionId($regionId)
                    ->asDefaultClient();
        
        $client = true;
    }
}