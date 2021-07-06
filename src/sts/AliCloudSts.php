<?php
namespace xudongyss\ali\sts;

use xudongyss\ali\base\AliCloudBase;
use AlibabaCloud\Sts\Sts;

/**
 * 阿里云 STS
 */
class AliCloudSts extends AliCloudBase{
    /**
     * oss
     * @param string    $accountID    阿里云账号ID。您可以通过登录阿里云控制台，将鼠标悬停在右上角头像的位置，单击安全设置进行查看
     * @param string    $roleName     RAM角色名称。您可以通过登录RAM控制台，单击左侧导航栏的RAM角色管理，在RAM角色名称列表下进行查看
     */
    public static function oss($accountID, $roleName, $action = ['oss:*'], $resource = ['*']) {
        try {
            $policy = [
                'Statement'=> [
                    [
                        'Action'=> $action,
                        'Effect'=> 'Allow',
                        'Resource'=> $resource
                    ],
                ],
                'Version'=> '1',
            ];
            $result = Sts::v20150401()
                         ->assumeRole()
                         ->withRoleArn('acs:ram::'.$accountID.':role/'.$roleName)
                         ->withRoleSessionName('app')
                         ->withPolicy(json_encode($policy))
                         ->request();
            
            return $result->toArray()['Credentials'];
        }catch(\Exception $e) {
            echo $e->getMessage();
        }
    }
}