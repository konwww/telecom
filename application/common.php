<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
require_once dirname(__DIR__) . '/vendor/api_sdk/vendor/autoload.php';

use Aliyun\Api\Dyvms\Request\V20170525\SingleCallByVoiceRequest;
use Aliyun\Core\Config;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
Config::load();
/**
 * 语音文件外呼
 *
 * @param $showNum
 * @param $phone
 * @param $voiceCode
 * @return stdClass
 */
function singleCallByVoice($showNum, $phone, $voiceCode)
{
    //产品名称:云通信语音服务API产品,开发者无需替换
    $product = "Dyvmsapi";

    //产品域名,开发者无需替换
    $domain = "dyvmsapi.aliyuncs.com";

    // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)

    $accessKeySecret = "yourAccessKeySecret"; // AccessKeySecret
    $accessKeyId = "yourAccessKeyId"; // AccessKeyId

    // 暂时不支持多Region
    $region = "cn-hangzhou";

    // 服务结点
    $endPointName = "cn-hangzhou";


    //初始化acsClient,暂不支持region化
    $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

    // 增加服务结点
    DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

    // 初始化AcsClient用于发起请求
    $acsClient = new DefaultAcsClient($profile);
    //组装请求对象-具体描述见控制台-文档部分内容
    $request = new SingleCallByVoiceRequest();
    //必填-被叫显号
    $request->setCalledShowNumber($showNum);
    //必填-被叫号码
    $request->setCalledNumber($phone);
    //必填-语音文件Code
    $request->setVoiceCode($voiceCode);
    //选填-外呼流水号
    $request->setOutId("1234");
    //hint 此处可能会抛出异常，注意catch
    $response = $acsClient->getAcsResponse($request);
    //aliyun sdk中使用了curl,避免拥塞所以sleep 1s
    sleep(1);
    return $response;
}