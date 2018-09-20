<?php
namespace LT\Duoduo\Task;

interface ITimeoutHandler{
    /**
     * @var NContext $context 
     * 
     */
    public  function processTimeout( $context);
  
}