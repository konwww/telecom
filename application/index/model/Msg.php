<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/22
 * Time: 9:15
 */

namespace app\index\model;


use app\index\model\aliyun\AliyunVms;
use think\Model;

class Msg extends Model
{
    public $voiceCode;
    public $autoWriteTimestamp = "Y-m-d H:i:s";
    private $vms;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $kind = \think\facade\Config::get("public");
        switch ($kind) {
            case "aliyun":
                //aliyunVms
                $this->vms = new AliyunVms();
                break;
            case "tencent":
                break;
            case "huawei":
                break;
        }
    }

    public function singleCall($phone)
    {
        $this->vms->singleCall($phone);
    }

    /**
     * @param $phone_list
     * @return bool|void
     */
    public function batchCall($phone_list)
    {
        if(empty($phone_list))return false;

        $this->vms->batchCall($phone_list);
    }

    /**
     * 通话状态
     * @param string $rid
     * @param string $phone
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function statusGetter($rid = "", $phone = "")
    {
        $msg_log = new MsgLog();
        $expression = [];
        if (empty($rid)) $expression["rid"] = $rid;
        if (empty($phone)) $expression["phone"] = $phone;
        $records = $msg_log->where($expression)->select();
        return $records;
    }

    /**
     *失败记录重拨
     * @param string $rid 为空表示重拨所有失败记录
     * @throws \think\Exception\DbException
     */
    public function redialByFailedRecords($rid)
    {
        //todo voiceCode
        $msg_log = new MsgLog();
        if (!empty($rid)) {
            $rid_list = array($rid);
        } else {
            $records = $msg_log->all();
            $rid_list = [];
            foreach ($records as $item) {
                $rid_list[] = $item["rid"];
            }
        }
        $this->vms->redial($rid_list);

    }

}