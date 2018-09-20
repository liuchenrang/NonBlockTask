<?php
namespace LT\Duoduo\Task;


class NLogger implements ILogger{
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