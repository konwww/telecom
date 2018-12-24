<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/23
 * Time: 18:09
 */

namespace app\index\controller;

set_time_limit(0);
use app\index\model\Excel;
use app\index\model\PhoneItem;
use think\App;
use think\Controller;

class Socket extends Controller
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }

    public function vmsStartCall(){
        $msg=new \app\index\model\Msg();
        $msg->batchCall();
    }

    /**
     * xlsx文件异步入队
     * @param $filename
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function xlsxQueuePush($filename){
        $filename = "../uploads/" . $filename;
        $data = Excel::import_excel($filename);
        //呼叫任务入队
        $phoneItem = new PhoneItem();
        foreach ($data as $item) {
            $phoneItem::create(["phone" => $item[1], "voiceCode" => $item[2], "memo" => $item[0]]);
        }
    }

}