<?php
namespace xudongyss\ali\base;

use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Util\ResponseChecker;

class AliPayBase{
    protected static $errorMessage = '';
    
    public static function getErrorMessage() {
        return static::$errorMessage;
    }
    
    protected static function setErrorMessage($errorMessage) {
        static::$errorMessage = $errorMessage;
    }
    
    protected static function config() {
        static $config;
        if($config) return $config;
        
        $config = new Config();
        
        return $config;
    }
    
    public static function setProtocol($protocol) {
        static::config()->protocol = $protocol;
    }
    
    public static function setGatewayHost($gatewayHost) {
        static::config()->gatewayHost = $gatewayHost;
    }
    
    public static function setSignType($signType) {
        static::config()->signType = $signType;
    }
    
    /**
     * AppId
     */
    public static function setAppId($appId) {
        static::config()->appId = $appId;
    }
    
    /**
     * 私钥
     * @param string    $merchantPrivateKey
     */
    public static function setMerchantPrivateKey($merchantPrivateKey) {
        static::config()->merchantPrivateKey = $merchantPrivateKey;
    }
    
    /**
     * 支付宝公钥
     */
    public static function setAlipayPublicKey($alipayPublicKey) {
        static::config()->alipayPublicKey = $alipayPublicKey;
    }
    
    /**
     * 初始化
     */
    public static function init($appId, $merchantPrivateKey = '', $alipayPublicKey = '') {
        /* 默认设置 */
        static::setProtocol('https');
        static::setGatewayHost('openapi.alipay.com');
        static::setSignType('RSA2');
        
        /* 自定义设置 */
        static::setAppId($appId);
        if($merchantPrivateKey) static::setMerchantPrivateKey($merchantPrivateKey);
        if($alipayPublicKey) static::setAlipayPublicKey($alipayPublicKey);
        
        Factory::setOptions(static::config());
    }
    
    /**
     * 返回值处理
     */
    protected static function response($result, $request) {
        $responseNode = str_replace('.', '_', $request->getApiMethodName()).'_response';
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode) && $resultCode == 10000) {
            return json_decode(json_encode($result->$responseNode), true);
        }else {
            static::setErrorMessage($result->$responseNode->sub_msg);
            return false;
        }
    }
    
    /**
     * 新版 SDK 处理响应
     */
    protected static function responseChecker($response) {
        $responseChecker = new ResponseChecker();
        if($responseChecker->success($response)) {
            return json_decode(json_encode($response), true);
        }
        
        static::setErrorMessage($response->subMsg);
        return false;
    }
}