<?php
namespace LT\Duoduo\Task;

interface ITaskTimeoutHandler{
    /**
     * @var NContext $context 
     * 
     */
    public  function processTimeout( $context);
  
}