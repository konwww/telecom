<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/23
 * Time: 18:48
 */

namespace app\index\model;


use think\Model;

class PhoneItem extends Model
{
    protected $table="ms_msg_queue";
    public $order_id;
    public $queue_id;
    public $phone;
    public $status=0;
    public $voiceCode;
    public $memo="";
    public $autoWriteTimestamp="datetime";
    public $pk="order_id";
}