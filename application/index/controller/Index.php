<?php
namespace app\index\controller;

use app\index\model\Xlsx;

class Index extends Validate
{
    public function index(){
        return $this->fetch();
    }
    public function status($phone=""){

    }
    public function callAgain($phone_list){

    }
    public function test(){

    }
}
