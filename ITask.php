<?php
namespace LT\Duoduo\Task;
interface ITask{
    /**
     * @param $instance
     * @param $method
     * @param array $params
     * @param null $ppid
     * @return mixed
     */
    public function fork($instance, $method, array $params=array(), $ppid=null);

}