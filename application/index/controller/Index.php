<?php
namespace app\index\controller;

class Index extends Validate
{
    public function index(){
        return $this->fetch("Index/upload");
    }
    public function status($phone=""){

    }
    public function redial($phone_list){

    }
    public function singleCall(){

    }
    public function batchCall(){

    }
}
