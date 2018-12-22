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

    public function send($filename)
    {
        $data = Xlsx::import_excel($filename);
        $msg_list = [];
        //重构数组
        foreach ($data as $item) {
            //通过比较获取差集(去除号码部分后剩余变量)
            $msg_list[$item[1]] = array_diff(array($item[1]), $item);
        }
        $msg=new \app\index\model\Msg();
        $msg->send($this->vms);
    }
}