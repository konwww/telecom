<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/21
 * Time: 21:11
 */

namespace app\index\controller;


use think\App;
use think\Controller;
use think\Response;

class User extends Controller
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);

    }

    public function login($username, $password, $verifyCode)
    {
        $validate = new \app\index\controller\validate\User();
        $data = [
            "username" => $username,
            "verifyCode" => $verifyCode
        ];
        if (!$validate->check($data)) {
            return Response::create(["errorMsg" => $validate->getError(), "replyContent" => ""], "JSON", 400);
        }
        $user = new \app\index\model\User();
        $user_data = $user->where(["username" => $username, "password" => md5($password)])->field("password", true)->find();
        if (empty($user_data)) {
            return Response::create([]);
        }
        $user_records = $user_data->getData();

        $this->setSession($user_records);
        return Response::create(["errorMsg" => "OK", "replyContent" => $user_records], "JSON", 200);
    }
    public function noLogin(){
        //todo display login view
        return $this->fetch();
    }
    public function changePwd($oldPassword,$newPassword){
        if (! $this->request->session("uid")){
            //todo no login
        }
        $user=new \app\index\model\User();
        $new_user=$user::update(["password"=>md5($newPassword)],["uid"=>$this->request->session("uid"),"password"=>md5($oldPassword)]);
        if (empty($new_user)){
            //todo found not this uid
        }
        //todo update is success
    }
    private function setSession($data)
    {
        foreach ($data as $key => $value) {
            \think\facade\Session::set($key, $value);
        }
    }
}