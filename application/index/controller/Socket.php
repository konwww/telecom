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
use think\Response;

class Socket extends Controller
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }

    public function vmsStartCall($secret){
        if ($secret!=="123891")return Response::create(["errorMsg"=>"api not found"],"json",404);
        $msg=new \app\index\model\Msg();
        $msg->batchCall();
        return Response::create(["errorMsg"=>"complete"],"json",200);

    }

    /**
     * xlsx文件异步入队
     * @param $filename
     * @param $voiceCode
     * @param $secret
     * @return Response
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function xlsxQueuePush($filename,$voiceCode,$secret){
        if ($secret!=="123890")return Response::create(["errorMsg"=>"api not found"],"json",404);
        $filename = "../uploads/" . $filename;
        $data = Excel::import_excel($filename);
        //呼叫任务入队
        $times_id=date("Y-m-d H:i:s");
        $phoneItem = new PhoneItem();
        foreach ($data as $item) {
            $phoneItem::create(["phone" => $item[1], "voiceCode" => $voiceCode, "memo" => $item[0],"times_id"=>$times_id]);
        }
    }

}