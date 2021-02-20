<?php
/**
 * 支付宝
 */
namespace XuDongYss\Ali\Pay;

use Alipay\EasySDK\Kernel\Factory;
use XuDongYss\Ali\Base\AliPayBase;

class AliPay extends AliPayBase{
    protected static $notifyData = [];
    
    /**
     * 获取异步通知全部参数
     */
    public static function getNotifyData() {
        return static::$notifyData;
    }
    
    /**
     * APP 下单
     * @param string        $outTradeNo         订单号
     * @param float         $totalAmount        订单金额
     * @param string        $notifyUrl          异步回调地址
     * @param string        $subject            订单标题
     * @param string(512)   $passbackParams     公用回传参数，如果请求时传递了该参数，则返回给商户时会回传该参数。支付宝会在异步通知时将该参数原样返回
     * @param int           $timeoutExpress     单位：分钟，该笔订单允许的最晚付款时间，逾期将关闭交易，取值：1 ~ 21600
     */
    public static function tradeAppPay($outTradeNo, $totalAmount, $notifyUrl, $subject, $passbackParams = '', $timeoutExpress = 10) {
        $optionalArgs = [];
        
        if($timeoutExpress < 1) $timeoutExpress = 1;
        if($timeoutExpress > 21600) $timeoutExpress = 21600;
        $timeoutExpress .= 'm';
        $optionalArgs['timeout_express'] = $timeoutExpress;
        if($passbackParams) $optionalArgs['passback_params'] = urlencode($passbackParams);
        
        $result = Factory::payment()
                         ->app()
                         ->asyncNotify($notifyUrl)
                         ->batchOptional($optionalArgs)
                         ->pay($subject, $outTradeNo, $totalAmount);
        
        return $result;
    }
    
    /**
     * 电脑网站下单
     * @param string        $outTradeNo         订单号
     * @param float         $totalAmount        订单金额
     * @param string        $notifyUrl          异步回调地址
     * @param string        $subject            订单标题
     * @param string        $returnUrl          同步回调地址
     * @param string(512)   $passbackParams     公用回传参数，如果请求时传递了该参数，则返回给商户时会回传该参数。支付宝会在异步通知时将该参数原样返回
     * @param int           $timeoutExpress     单位：分钟，该笔订单允许的最晚付款时间，逾期将关闭交易，取值：1 ~ 21600
     */
    public static function tradePagePay($outTradeNo, $totalAmount, $notifyUrl, $subject, $returnUrl = '', $passbackParams = '', $timeoutExpress = 10) {
        $optionalArgs = [];
        
        if($timeoutExpress < 1) $timeoutExpress = 1;
        if($timeoutExpress > 21600) $timeoutExpress = 21600;
        $timeoutExpress .= 'm';
        $optionalArgs['timeout_express'] = $timeoutExpress;
        if($passbackParams) $optionalArgs['passback_params'] = urlencode($passbackParams);
        
        $result = Factory::payment()
                         ->page()
                         ->asyncNotify($notifyUrl)
                         ->batchOptional($optionalArgs)
                         ->pay($subject, $outTradeNo, $totalAmount, $returnUrl);
        
        return $result;
    }
    
    /**
     * 手机网站下单
     * @param string    $outTradeNo     订单号
     * @param float     $totalAmount    订单金额
     * @param string    $notifyUrl      异步回调地址
     * @param string    $subject        商品说明
     * @param string    $quitUrl        用户付款中途退出返回商户网站的地址
     * @param string    $returnUrl      同步回调地址
     */
    public static function tradeWapPay($outTradeNo, $totalAmount, $notifyUrl, $subject, $quitUrl, $returnUrl = '', $passbackParams = '', $timeoutExpress = 10) {
        $result = Factory::payment()
                         ->wap()
                         ->asyncNotify($notifyUrl)
                         ->pay($subject, $outTradeNo, $totalAmount, $quitUrl, $returnUrl);
        
        return $result;
    }
    
    /**
     * 异步通知校验
     */
    public static function verifyNotify() {
        static::$notifyData = $_POST;
        $result = Factory::payment()
               ->common()
               ->verifyNotify(static::$notifyData);
        
        if($result === true) {
            /* 交易支付成功 */
            if(static::$notifyData['trade_status'] === 'TRADE_SUCCESS') {
                return static::notifyData(static::$notifyData);
            }
        }
        
        return false;
    }
    
    /**
     * 异步通知参数转化
     */
    protected static function notifyData($notifyData) {
        $_data = [
            'order_no'=> $notifyData['out_trade_no'],
            'total_amount'=> $notifyData['total_amount'],
            'pay_no'=> $notifyData['trade_no'],
            'pay_time'=> $notifyData['gmt_payment'],
        ];
        
        return $_data;
    }
    
    /**
     * 数据校验
     * @param float     $totalAmount    订单金额
     * @param string    $appId          
     * @return boolean
     */
    public static function verifyNotifyData($totalAmount, $appId = '') {
        if(static::$notifyData['total_amount'] * 100 != $totalAmount * 100) return false;
        if($appId && static::$notifyData['app_id'] != $appId) return false;
        
        return true;
    }
    
    /**
     * 异步通知处理成功
     */
    public static function success() {
        echo 'success';
        exit();
    }
    
    /**
     * 交易退款
     * @param string    $outTradeNo     交易创建时传入的商户订单号
     * @param float     $refundAmount   需要退款的金额，该金额不能大于订单金额，单位为元，支持两位小数  
     */
    public static function tradeRefund($outTradeNo, $refundAmount) {
        $result = Factory::payment()
               ->common()
               ->refund($outTradeNo, $refundAmount);
        
        return static::responseChecker($result);
    }
}