<?php
namespace LT\Duoduo\Task;
interface ITask{
    /**
     * @param $method
     * @param array $params
     * @param null $ppid
     * @return mixed
     */
    public function fork($method, array $params=array(), $ppid=null);

}