<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/22
 * Time: 9:46
 */

namespace app\index\model\aliyun;


use Aliyun\Api\Dyvms\Request\V20170525\SingleCallByVoiceRequest;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use app\index\model\MsgLog;
use app\index\model\PhoneItem;
use app\index\model\VmsInterface;
use stdClass;
use think\facade\Config;
use think\Model;

class AliyunVms extends Model implements VmsInterface
{
    public $accessKeySecret;
    public $accessKeyId;
    public $showNum;
    public $voiceCode;
    public $id;
    protected $table;
    public $autoWriteTimestamp="datetime";

    public function __construct($data = [])
    {
        parent::__construct($data);
        $vars = Config::get("aliyun_base_config");
        $this->accessKeySecret = $vars["accessKeySecret"];
        $this->accessKeyId = $vars["accessKeyId"];
        $this->showNum = $vars["showNum"];
        $this->voiceCode=$vars["voiceCode"];
    }

    /**
     * @param $phone
     * @return MsgLog
     */
    public function singleCall($phone)
    {
        //单独发送一条语音通知
        $response = self::singleCallByVoice($this->showNum, $phone, $this->voiceCode);
        return $this->status($phone, $response);
    }

    public function batchCall($phone_list)
    {
        $phoneItem=new PhoneItem();
        foreach ($phone_list as $key =>$item) {
            //锁定队列中出队元素
            $item_temp=$phoneItem::update(["status"=>1],$item);
            $response = self::singleCallByVoice($this->showNum, $item["phone"],$item["voiceCode"]);
            $this->status($item["phone"], $response);
            //出队
            $item_temp->save(["status"=>2]);
        }
    }
    /**
     * @param $phone
     * @param $response
     * @return MsgLog
     */
    public function status($phone, $response)
    {
        $status = $response["code"] == "OK" ? "success" : "failed";
        return MsgLog::create(
            [
                "status" => $status,
                "phone" => $phone,
                "errorMsg" => "code: " . $response["code"] . " ;message: " . $response["message"],
                "remarks" => "RequestId: " . $response["RequestId"] . ";Called: " . $response["called"]
            ]);
    }

    /**
     * @param $rid_list
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function redial($rid_list){
        $msg_log=new MsgLog();
        $data=$msg_log->whereIn("rid",$rid_list)->field(["rid","phone","voiceCode"])->select();
        foreach ($data as $item){
            $response = self::singleCallByVoice($this->showNum, $item["phone"], $item["voiceCode"]);
            //更新通话状态
            $status = $response["code"] == "OK" ? "success" : "failed";
            $msg_log::update([
                "status" => $status,
                "errorMsg" => "code: " . $response["code"] . " ;message: " . $response["message"],
                "remarks" => "RequestId: " . $response["RequestId"] . ";Called: " . $response["called"]
                ,"voiceCode"=>$item["voiceCode"]
            ]);
        }
    }

    /**
     * 语音文件外呼
     *
     * @param $showNum
     * @param $phone
     * @param $voiceCode
     * @return stdClass
     */
    private static function singleCallByVoice($showNum, $phone, $voiceCode)
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


        //初始化acsCl ient,暂不支持region化
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


}