<?php
namespace LT\Duoduo\Task;
/**
 * Interface ITaskTimeoutHandler
 * @package LT\Duoduo\Task
 * @author duoduo
 */
interface ITaskTimeoutHandler{
    /**
     * @var NContext $context 
     * 超时发生时， 怎么处理进程
     */
    public  function processTimeout( $context);
  
}