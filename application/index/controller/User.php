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

class User extends Controller
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }
    public function login($username,$password,$verifyCode){

    }
}