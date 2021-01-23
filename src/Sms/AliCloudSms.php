<?php
/**
 * 阿里云 - 短信
 */
namespace XuDongYss\Ali\Sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use XuDongYss\Ali\base\AliCloudBase;

class AliCloudSms extends AliCloudBase{
    /* 基本设置 */
    protected const PRODUCT = 'Dysmsapi';
    protected const VERSION = '2017-05-25';
    protected const HOST = 'dysmsapi.aliyuncs.com';
    protected const REGIONID = 'cn-hangzhou';
    
    /**
     * 发送短信验证码
     * @param string    $mobile         手机号
     * @param string    $code           短信验证码
     * @param string    $templateCode   短信模板
     * @param string    $signName       短信签名
     * @param []		$extend			短信模板中的自定义参数，除 code 外
     * @return boolean  发送成功返回 true, 失败返回错误信息
     */
    public static function sendSms($mobile, $code, $templateCode, $signName, array $extend = []) {
        try {
        	$templateParam = ['code'=> $code];
        	if($extend) $templateParam = array_merge($templateParam, $extend);
        	
            $options = [
                'query'=> [
                    'RegionId' => static::REGIONID,
                    'PhoneNumbers'=> $mobile,
                    'SignName'=> $signName ? $signName : static::$signName,
                    'TemplateCode'=> $templateCode,
                	'TemplateParam'=> json_encode($templateParam),
                ],
            ];
            
            $result = AlibabaCloud::rpc()
                                  ->product(static::PRODUCT)
                                  ->version(static::VERSION)
                                  ->action('SendSms')
                                  ->method('POST')
                                  ->host(static::HOST)
                                  ->options($options)
                                  ->request();
            
            return static::response($result);
        }catch(ClientException $e) {
            static::setErrorMessage($e->getErrorMessage());
            
            return false;
        }catch(ServerException $e) {
            static::setErrorMessage($e->getErrorMessage());
            
            return false;
        }
    }
    
    /**
     * 批量发送短信
     * @param []        $mobile         接收短信的手机号码
     * @param []        $param          短信模板变量对应的实际值
     * @param string    $templateCode   短信模板
     * @param []        $signName       短信签名名称
     */
    public static function sendBatchSms($mobile, $param, $templateCode, $signName) {
        try {
            $options = [
                'query'=> [
                    'RegionId' => static::REGIONID,
                    'PhoneNumberJson'=> json_encode($mobile),
                    'SignNameJson'=> json_encode($signName),
                    'TemplateCode'=> $templateCode,
                    'TemplateParamJson'=> json_encode($param),
                ],
            ];
            
            $result = AlibabaCloud::rpc()
                                  ->product(static::PRODUCT)
                                  ->version(static::VERSION)
                                  ->action('SendBatchSms')
                                  ->method('POST')
                                  ->host(static::HOST)
                                  ->options($options)
                                  ->request();
            
            return static::response($result);
        }catch(ClientException $e) {
            static::setErrorMessage($e->getErrorMessage());
            
            return false;
        }catch(ServerException $e) {
            static::setErrorMessage($e->getErrorMessage());
            
            return false;
        }
    }
    
    /**
     * 添加短信模板
     * @param string    $name         模板名称，长度为1~30个字符
     * @param string    $content      模板内容，长度为1~500个字符
     * @param string    $remark       短信模板申请说明
     * @param string    $type         短信类型
     *                                  0：验证码。
     *                                  1：短信通知。
     *                                  2：推广短信。
     *                                  3：国际/港澳台消息。
     */
    public static function addSmsTemplate($name, $content, $remark, $type = 0) {
        try {
            $options = [
                'query'=> [
                    'RegionId' => static::REGIONID,
                    'TemplateType'=> $type,
                    'TemplateName'=> $name,
                    'TemplateContent'=> $content,
                    'Remark'=> $remark,
                ],
            ];
            
            $result = AlibabaCloud::rpc()
                                  ->product(static::PRODUCT)
                                  ->version(static::VERSION)
                                  ->action('AddSmsTemplate')
                                  ->method('POST')
                                  ->host(static::HOST)
                                  ->options($options)
                                  ->request();
            
            return static::response($result);
        }catch(ClientException $e) {
            static::setErrorMessage($e->getErrorMessage());
            
            return false;
        }catch(ServerException $e) {
            static::setErrorMessage($e->getErrorMessage());
            
            return false;
        }
    }
    
    /**
     * 查看短信发送记录和发送状态
     * @param string    $mobile         接收短信的手机号码
     * @param string    $sendDate       短信发送日期，支持查询最近30天的记录。格式为yyyyMMdd，例如20181225
     * @param int       $currentPage    分页查看发送记录，指定发送记录的的当前页码
     * @param int       $pageSize       分页查看发送记录，指定每页显示的短信记录数量。取值范围为1~50。
     */
    public static function querySendDetails($mobile, $sendDate, $currentPage = 1, $pageSize = 50) {
        try {
            $options = [
                'query'=> [
                    'RegionId' => static::REGIONID,
                    'PhoneNumber'=> $mobile,
                    'SendDate'=> $sendDate,
                    'PageSize'=> $pageSize,
                    'CurrentPage'=> $currentPage,
                ],
            ];
            
            $result = AlibabaCloud::rpc()
                                  ->product(static::PRODUCT)
                                  ->version(static::VERSION)
                                  ->action('QuerySendDetails')
                                  ->method('POST')
                                  ->host(static::HOST)
                                  ->options($options)
                                  ->request();
            
            return static::response($result);
        }catch(ClientException $e) {
            static::setErrorMessage($e->getErrorMessage());
            
            return false;
        }catch(ServerException $e) {
            static::setErrorMessage($e->getErrorMessage());
            
            return false;
        }
    }
    
    public static function response($response) {
        if($response['Code'] === 'OK') {
            return $response->toArray();
        }
        
        static::setErrorMessage($response['Message']);
        return false;
    }
}