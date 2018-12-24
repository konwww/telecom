<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/23
 * Time: 20:01
 */

namespace app\index\model;


use think\Model;

class QueueLog extends Model
{
    public $id;
    public $autoWriteTimestamp="datetime";
    public $times_id;
    public $memo;

}