<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/22
 * Time: 9:50
 */

namespace app\index\controller;


use app\index\model\aliyun\AliyunVms;
use app\index\model\Xlsx;
use think\App;

class Msg extends Validate
{
    public $vms;
    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }

    /**
     * excel文件批量呼叫
     * @param $filename
     */
    public function batchCall($filename)
    {
        $data = Xlsx::import_excel($filename);
        $phone_list=[];
        //重构数组
        foreach ($data as $item) {
            //通过比较获取差集(去除号码部分后剩余变量)
            $phone_list[] = $item[1];
        }
        $msg=new \app\index\model\Msg();
        $msg->batchCall($phone_list);
    }

    /**
     * 单呼
     * @param $phone
     */
    public function singleCall($phone){

    }

    /**
     * 重拨
     * @param $rid
     */
    public function redial($rid){

    }
}