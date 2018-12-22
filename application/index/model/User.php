<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/21
 * Time: 21:22
 */

namespace app\index\model;


use think\Model;

class User extends Model
{
    protected $table="ms_user";
    public $uid;
    public $username;
    public $password;
    public $level;
    protected $create_at;
    protected $update_at;
    public $autoWriteTimestamp="Y-m-d H:i:s";
    protected $createTime="create_at";
    protected $updateTime="update_at";
    public function __construct($data = [])
    {
        parent::__construct($data);
    }
}