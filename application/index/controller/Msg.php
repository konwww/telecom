<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/22
 * Time: 9:50
 */

namespace app\index\controller;


use app\index\model\Excel;
use app\index\model\MsgLog;
use app\index\model\PhoneItem;
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
     * @param $voiceCode
     * @return string
     */

    public function batchCall($filename, $voiceCode)
    {
        $filename = "../uploads/" . $filename;
//        $url = parse_url($this->request->domain()."/index.php/api/queue_push?filename=".urlencode($filename));
//        dump($url);
        $url=$this->request->domain()."/index.php/api/queue_push";
        return $this->sock_data($url,80,null,null,["filename"=>$filename]);
    }

    /**fsockopen 抓取页面
     * @parem $url 网页地址 host 主机地址
     * @parem $port 网址端口 默认80
     * @parem $t 脚本请求时间 默认30s
     * @parem $method 请求方式 get/post
     * @parem $data 如果单独传数据为 post 方式
     * @return 返回请求回的数据
     * */
    function sock_data($url,$port=80,$t=30,$method='get',$data=null)
    {
        $info=parse_url($url);
        $fp = fsockopen($info["host"],$port, $errno, $errstr,$t);
        stream_set_blocking($fp,true);//开启了手册上说的非阻塞模式
        stream_set_timeout($fp,1);//设置超时
        // 判断是否有数据
        if(isset($data) && !empty($data))
        {
            $query = http_build_query($data); // 数组转url 字符串形式
        }else
        {
            $query=null;
        }
        // 如果用户的$url "http://www.manongjc.com/";  缺少 最后的反斜杠
        if(!isset($info['path']) || empty($info['path']))
        {
            $info['path']="/index.html";
        }
        // 判断 请求方式
        if($method=='post')
        {
            $head = "POST ".$info['path']." HTTP/1.0".PHP_EOL;
        }else
        {
            $head = "GET ".$info['path']."?".$query." HTTP/1.0".PHP_EOL;
        }

        $head .= "Host: ".$info['host'].PHP_EOL; // 请求主机地址
        $head .= "Referer: http://".$info['host'].$info['path'].PHP_EOL;
        if(isset($data) && !empty($data) && ($method=='post'))
        {
            $head .= "Content-type: application/x-www-form-urlencoded".PHP_EOL;
            $head .= "Content-Length: ".strlen(trim($query)).PHP_EOL;
            $head .= PHP_EOL;
            $head .= trim($query);
        }else
        {
            $head .= PHP_EOL;
        }
        $write = fputs($fp, $head); //写入文件(可安全用于二进制文件)。 fputs() 函数是 fwrite() 函数的别名
        dump($write);
        //使nginx异步生效
        usleep(1000);
//        while (!feof($fp))
//        {
//            $line = fread($fp,4096);
//            echo $line;
//        }
    }
    /**
     * 单呼
     * @param $phone
     * @param $voiceCode
     * @param $memo
     */
    public function singleCall($phone, $voiceCode, $memo)
    {
        $phoneItem = new PhoneItem();
        $phoneItem::create(["phone" => $phone, "voiceCode" => $voiceCode, "memo" => $memo]);
    }

    /**
     * 重拨
     * @param $queue_id
     */
    public function createCalledRecords($queue_id)
    {
        $msg_log = new MsgLog();
        $title = ["mid", "status", ""];
        $called_data = array_values($msg_log->where("queue_id", $queue_id)->select());
        dump($called_data);

    }

    /**
     *
     */
    public function upload()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file("sheet");
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move('../uploads');
        if ($info) {
            // 成功上传后 获取上传信息
            $this->batchCall($info->getSaveName(), "test.wav");
        } else {
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

}