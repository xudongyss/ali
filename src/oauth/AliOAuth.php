<?php

namespace xudongyss\ali\oauth;

use xudongyss\ali\base\AliPayBase;
use Alipay\EasySDK\Kernel\Factory;
use ali\pay\request\AlipayUserInfoShareRequest;

/**
 * 支付授权登录
 */
class AliOAuth extends AliPayBase{
    /**
     * 授权 url
     * @param string    $redirect_uri  回调页面
     * @param string    $state         商户自定义参数，用户授权后，重定向到 redirect_uri 时会原样回传给商户。 
     *                                 为防止 CSRF 攻击，建议开发者请求授权时传入 state 参数，
     *                                 该参数要做到既不可预测，又可以证明客户端和当前第三方网站的登录认证状态存在关联，并且不能有中文
     * $scope string    $scope         auth_base：静默授权，用户授权并自动跳转到回调页的。用户感知的就是直接进入了回调页
     *                                 auth_user：以 auth_user 为 scope 发起的网页授权，是用来获取用户的基本信息的（比如头像、昵称等）。
     *                                 但这种授权需要用户手动同意；
     *                                 用户同意后，就可在授权后获取到该用户的基本信息。
     *                                 若想获取用户信息，scope 的值中需要有该值存在，如 scope=auth_user,auth_base
     * @return string
     */
    public static function oauthUrl($redirect_uri, $state = '', $scope = 'auth_user,auth_base') {
        $host = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm';
        
        $_data = [
            'app_id'=> static::config()->appId,
            'scope'=> $scope,
            'redirect_uri'=> $redirect_uri,
            'state'=> $state,
        ];
        
        return $host.'?'.http_build_query($_data);
    }
    
    /**
     * 获取授权访问令牌
     * @param string            $code   授权码，用户对应用授权后得到，auth_code
     * @return boolean|[]    
     */
    public static function getAccessToken($code) {
        try {
            $response = Factory::base()->oauth()->getToken($code);
            
            return static::responseChecker($response);
        }catch(\Exception $e) {
            static::setErrorMessage($e->getMessage());
            
            return false;
        }
    }
    
    /**
     * 获取用户信息
     * @param string        $authToken  通过auth_code获取的access_token
     * @return boolean|[]
     */
    public static function getUserInfo($authToken) {
        $request = new AlipayUserInfoShareRequest();
        try {
            $result = static::aop()->execute($request, $authToken);
            
            return static::response($result, $request);
        }catch(\Exception $e) {
            static::setErrorMessage($e->getMessage());
            
            return false;
        }
        
    }
}