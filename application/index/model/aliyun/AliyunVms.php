<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/22
 * Time: 9:46
 */

namespace app\index\model\aliyun;


use app\index\model\MsgLog;
use app\index\model\PhoneItem;
use app\index\model\VmsInterface;
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
    public $autoWriteTimestamp = "datetime";

    public function __construct($data = [])
    {
        parent::__construct($data);
        $vars = Config::get("aliyun_base_config");
        $this->accessKeySecret = $vars["accessKeySecret"];
        $this->accessKeyId = $vars["accessKeyId"];
        $this->showNum = $vars["showNum"];
        $this->voiceCode = $vars["voiceCode"];
    }

    /**
     * @param $phone
     * @return MsgLog
     */
    public function singleCall($phone)
    {
        //单独发送一条语音通知
        $response = singleCallByVoice($this->showNum, $phone, $this->voiceCode);
        return $this->status($phone, $response);
    }

    public function batchCall($phone_list)
    {
        $phoneItem = new PhoneItem();
        foreach ($phone_list as $key => $item) {
            //锁定队列中出队元素
            $item_temp = $phoneItem::update(["status" => 1], ["order_id" => $item["order_id"]]);
            $response = singleCallByVoice($this->showNum, $item["phone"], $item["voiceCode"]);
            $this->status($item["phone"], $response);
            //出队
            $item_temp->save(["status" => 2], ["order_id" => $item["order_id"]]);
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
    public function redial($rid_list)
    {
        $msg_log = new MsgLog();
        $data = $msg_log->whereIn("rid", $rid_list)->field(["rid", "phone", "voiceCode"])->select();
        foreach ($data as $item) {
            $response = singleCallByVoice($this->showNum, $item["phone"], $item["voiceCode"]);
            //更新通话状态
            $status = $response["code"] == "OK" ? "success" : "failed";
            $msg_log::update([
                "status" => $status,
                "errorMsg" => "code: " . $response["code"] . " ;message: " . $response["message"],
                "remarks" => "RequestId: " . $response["RequestId"] . ";Called: " . $response["called"]
                , "voiceCode" => $item["voiceCode"]
            ]);
        }
    }




}