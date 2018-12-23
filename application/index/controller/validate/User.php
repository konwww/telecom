<?php
/**
 * Created by PhpStorm.
 * User: Dell 7275
 * Date: 2018/12/23
 * Time: 10:29
 */

namespace app\index\controller\validate;




use think\Validate;

class User extends Validate
{
    protected $rule=[
        "username"=>"required|max:25",
        "verifyCode"=>"required|captcha"

    ];
    protected $message=[
        "username.max"=>"username 必须小于25个字符",
        "username.required"=>"username 必须存在",
        "verifyCode"=>"验证码错误"
    ];
    public function login(){

    }

}