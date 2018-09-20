<?php
namespace LT\Duoduo\Task;

interface ILogger{
    public  function debug( $var = '');
    public  function notify( $var = '');
  
}