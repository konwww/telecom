<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/21
 * Time: 20:55
 */

namespace app\index\controller;


use think\App;
use think\Controller;

class Validate extends Controller
{
    public $uid;
    public $username;
    public $level;
    public function __construct(App $app = null)
    {
        parent::__construct($app);
//        $this->isLogin();
        $this->setRequestCookie();
    }
    private function isLogin(){
        $uid=$this->request->session("uid");
        if (empty($uid)){
            \think\facade\Url::root($this->request->domain()."/index.php");
            $this->error("未登录用户",url("User/login"));
        }else{
            $this->uid=$uid;
            $this->username=$this->request->session("username");
            $this->level=$this->request->session("level");
        }
    }
    public function setRequestCookie(){
        \think\facade\Cookie::set("request_time",time());
    }

}