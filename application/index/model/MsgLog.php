<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/22
 * Time: 10:21
 */

namespace app\index\model;


use think\Model;

class MsgLog extends Model
{
    protected $table="ms_msg_log";
    public $mid;
    public $status;
    public $phone;
    public $errorMsg;
    public $remarks;
    public $voiceCode;
    public $times_id;
    public $autoWriteTimestamp="datetime";
    protected $pk="mid";
}