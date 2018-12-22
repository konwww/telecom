<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/22
 * Time: 9:31
 */

namespace app\index\model;


interface VmsInterface
{
    public function singleCall($phone,$voiceCode="");
    public function status($phone,$response);
    public function batchCall($phone_list,$voiceCode="");
}