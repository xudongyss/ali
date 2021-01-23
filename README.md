# 阿里系相关SDK

[TOC]

## 安装

```
composer require xudongyss/ali
```
## 快速使用

### 支付宝 支付

#### 初始化
```php
require_once 'vendor/autoload.php';

use XuDongYss\Ali\Pay\AliPay;

$appId = '';
/* 应用私钥 */
$merchantPrivateKey = '';
/* 支付宝公钥 */
$alipayPublicKey = '';

AliPay::init($appId, $merchantPrivateKey, $alipayPublicKey);
```
#### PC 网站
```php
/* 订单号 */
$outTradeNo = '';
/* 订单金额 */
$totalAmount = 1;
/* 异步回调地址 */
$notifyUrl = '';
/* 订单标题 */
$subject = '';
/* 可选：同步跳转地址 */
$returnUrl = '';
/* 可选：公用回传参数，如果请求时传递了该参数，则返回给商户时会回传该参数。支付宝会在异步通知时将该参数原样返回 */
$passbackParams = '';
/* 可选：单位：分钟，该笔订单允许的最晚付款时间，逾期将关闭交易，取值：1 ~ 21600，默认值：10 */
$timeoutExpress = 10;
AliPay::tradePagePay($outTradeNo, $totalAmount, $notifyUrl, $subject, $returnUrl, $passbackParams, $timeoutExpress);
//返回值：html，可自动跳转的 POST 表单
```
#### APP 支付
```php
/* 订单号 */
$outTradeNo = '';
/* 订单金额 */
$totalAmount = 1;
/* 异步回调地址 */
$notifyUrl = '';
/* 订单标题 */
$subject = '';
/* 可选：公用回传参数，如果请求时传递了该参数，则返回给商户时会回传该参数。支付宝会在异步通知时将该参数原样返回 */
$passbackParams = '';
/* 可选：单位：分钟，该笔订单允许的最晚付款时间，逾期将关闭交易，取值：1 ~ 21600，默认值：10 */
$timeoutExpress = 10;
AliPay::tradeAppPay($outTradeNo, $totalAmount, $notifyUrl, $subject, $passbackParams, $timeoutExpress);
//返回值：APP 可直接使用返回调用支付，无需进行任何处理
```
#### 手机网站 支付
```php
/* 订单号 */
$outTradeNo = '';
/* 订单金额 */
$totalAmount = 1;
/* 异步回调地址 */
$notifyUrl = '';
/* 订单标题 */
$subject = '';
/* 用户付款中途退出返回商户网站的地址 */
$quitUrl = '';
/* 可选：同步跳转地址 */
$returnUrl = '';
/* 可选：公用回传参数，如果请求时传递了该参数，则返回给商户时会回传该参数。支付宝会在异步通知时将该参数原样返回 */
$passbackParams = '';
/* 可选：单位：分钟，该笔订单允许的最晚付款时间，逾期将关闭交易，取值：1 ~ 21600，默认值：10 */
$timeoutExpress = 10;
AliPay::tradeWapPay($outTradeNo, $totalAmount, $notifyUrl, $subject, $quitUrl, $returnUrl, $passbackParams, $timeoutExpress);
//返回值：html，可自动跳转的 POST 表单
```

#### 异步回调

```php
AliPay::verifyNotify();
//返回值：异常返回 false, 使用 AliPay::getErrorMessage() 获取错误描述
//正常返回：原始数据：使用 AliPay::getNotifyData() 获取
Array
(
    [order_no] => 订单号
    [total_amount] => 订单金额
    [pay_no] => 支付交易号
    [pay_time] => 支付时间
)
//业务处理完成后
AliPay::success();//调用后，请不要有任何输出
```

#### 交易退款

```php
/* 交易创建时传入的商户订单号 */
$outTradeNo = '';
/* 需要退款的金额，该金额不能大于订单金额，单位为元，支持两位小数 */
$refundAmount = 1;
AliPay::tradeRefund($outTradeNo, $refundAmount);
//返回值：异常返回 false, 使用 AliPay::getErrorMessage() 获取错误描述
//正常返回
```

### 支付宝 授权登录

#### 初始化

```php
require_once 'vendor/autoload.php';

use XuDongYss\Ali\Oauth\AliOAuth;

$appId = '';
/* 应用私钥 */
$merchantPrivateKey = '';
/* 支付宝公钥 */
$alipayPublicKey = '';

AliOAuth::init($appId, $merchantPrivateKey, $alipayPublicKey);
```

#### 获取授权 URL

```php
/* 回调页面 */
$redirect_uri = '';
/*
 * 商户自定义参数，用户授权后，重定向到 redirect_uri 时会原样回传给商户。 
 * 为防止 CSRF 攻击，建议开发者请求授权时传入 state 参数，
 * 该参数要做到既不可预测，又可以证明客户端和当前第三方网站的登录认证状态存在关联，并且不能有中文
 */
$state = '';
/* 
 * 可选：默认值：auth_user,auth_base
 * auth_base：静默授权，用户授权并自动跳转到回调页的。用户感知的就是直接进入了回调页
 * auth_user：以 auth_user 为 scope 发起的网页授权，是用来获取用户的基本信息的（比如头像、昵称等）。
 * 但这种授权需要用户手动同意；用户同意后，就可在授权后获取到该用户的基本信息。
 * 若想获取用户信息，scope 的值中需要有该值存在，如 scope=auth_user,auth_base
 */
$scope = '';
AliOAuth::oauthUrl($redirect_uri, $state, $scope);
```

#### 获取授权访问令牌

```php
/* 授权码，用户对应用授权后得到，auth_code */
$code = '';
AliOAuth::getAccessToken($code);
//返回值
//异常返回：false, 使用 AliOAuth::getErrorMessage() 获取错误描述
//正常返回
Array
(
    [user_id] => 支付宝用户的唯一userId
    [access_token] => 访问令牌。通过该令牌调用需要授权类接口
    [expires_in] => 访问令牌的有效时间，单位是秒
    [refresh_token] => 刷新令牌。通过该令牌可以刷新access_token
    [re_expires_in] => 刷新令牌的有效时间，单位是秒
)
```

#### 获取用户信息

```php
/* 通过auth_code获取的access_token */
$authToken = '';
AliOAuth::getUserInfo($authToken);
//返回值
//异常返回：false, 使用 AliOAuth::getErrorMessage() 获取错误描述
//正常返回
Array
(
    [user_id] => 支付宝用户的唯一userId
    [avatar] => 用户头像地址
    [province] => 省份名称
    [city] => 市名称
    [nick_name] => 用户昵称
    [gender]=> 【注意】只有is_certified为T的时候才有意义，否则不保证准确性.性别（F：女性；M：男性）。
)
```

### 阿里云 短信

#### 初始化

```php
require_once 'vendor/autoload.php';

use XuDongYss\Ali\Sms\AliCloudSms;

$accessKeyId = '';
$accessSecret = '';
AliCloudSms::init($accessKeyId, $accessSecret);
```

#### 发送短信验证码

```php
/* 手机号 */
$mobile = '';
/* 短信验证码 */
$code = '';
/* 短信模板 */
$templateCode = '';
/* 短信签名 */
$signName = '';
/* 短信模板中的自定义参数，除 code 外。非必要参数 */
$extend = [];
/* 成功返回接口返回值数组, 失败返回 false, 可通过 getErrorMessage 方法回去错误提示 */
$result = AliCloudSms::sendSms($mobile, $code, $templateCode, $signName, $extend);
echo '<pre>';print_r($result);
//
```

### 阿里云 STS

#### 初始化

```php
require_once 'vendor/autoload.php';

use XuDongYss\Ali\Sts\AliCloudSts;

$accessKeyId = '';
$accessSecret = '';
/* 地域 ID: https://help.aliyun.com/document_detail/66053.html?spm=a2c4g.11186623.6.790.703c39afrZf5UX */
$regionId = 'cn-shanghai';
AliCloudSts::init($accessKeyId, $accessSecret, 'cn-shanghai');
```

#### OSS

```php
/* 阿里云账号ID。您可以通过登录阿里云控制台，将鼠标悬停在右上角头像的位置，单击安全设置进行查看 */
$accountID = '1522108238103348';
/* RAM角色名称。您可以通过登录RAM控制台，单击左侧导航栏的RAM角色管理，在RAM角色名称列表下进行查看 */
$roleName = 'app';
$sts = AliCloudSts::oss($accountID, $roleName);
//返回值
//异常返回：false, 使用 AliCloudSts::getErrorMessage() 获取错误描述
//正常返回
Array
(
    [SecurityToken] => 
    [AccessKeyId] => 
    [AccessKeySecret] => 
    [Expiration] => 2020-07-20T04:38:28Z
)
```

### 阿里云 OSS

#### 初始化

##### 快速初始化

```php
require_once 'vendor/autoload.php';

use XuDongYss\Ali\Oss\AliCloudOss;

$AccessKeyId = '';
$AccessKeySecret = '';
$endpoint = 'oss-cn-shanghai.aliyuncs.com';
AliCloudOss::client($AccessKeyId, $AccessKeySecret, $endpoint,);
```

##### STS临时授权访问OSS(推荐使用)

```php
/* AliCloudSts::oss 的返回值 */
$sts = [];
/* https://help.aliyun.com/document_detail/31837.html?spm=a2c4g.11186623.2.14.4e69221b8az2Ld#concept-zt4-cvy-5db */
$endpoint = 'oss-cn-shanghai.aliyuncs.com';
AliCloudOss::client($sts['AccessKeyId'], $sts['AccessKeySecret'], $endpoint, $sts['SecurityToken']);
```

#### 上传文件

```php
/* 存储空间名称 */
$bucket = '';
/* 文件名称 */
$object = '';
/* 本地文件：本地文件路径加文件名包括后缀组成 */
$file = '';
$oss = AliCloudOss::uploadFile($bucket, $object, $file);
//返回值
//异常返回：false, 使用 AliCloudOss::getErrorMessage() 获取错误描述
```

