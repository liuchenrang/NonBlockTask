<?php
namespace LT\Duoduo\Task;
interface ITask{
    /**
     * fork 给定对象的方法
     * 
     * @param $instance
     * @param $method
     * @param array $params
     * @param null $ppid
     * @return mixed
     */
    public function fork($instance, $method, array $params=array(), $ppid=null);

}