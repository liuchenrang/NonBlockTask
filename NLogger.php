<?php
namespace LT\Duoduo\Task;

interface Logger{
    public  function info( $var = '');
    public  function notify( $var = '');
  
}

class Logger implements ILogger{
    public  function debug( $var = '')
    {
        $info = sprintf("%s %s\r\n",date("Y-m-d H:i:s"),$var);
        echo $info;
    }
    public  function notify( $var = '')
    {
        $info = sprintf("%s %s\r\n",date("Y-m-d H:i:s"),$var);
        echo $info;
    }
}