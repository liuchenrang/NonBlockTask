<?php
namespace LT\Duoduo\Task;
interface ITask{
    /**
     * return pid
     */
    public function fork($method, array $params=array(), $ppid=null);

}