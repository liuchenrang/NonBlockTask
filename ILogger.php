<?php
namespace LT\Duoduo\Task;

interface ILogger{
    /**
     * @param string $var
     * 输出程序运行过程中的日志信息
     * @return mixed
     */
    public  function debug( $var = '');

    /**
     * @param string $var
     * 发送通知信息（例如进程超时运行， 进程信息）
     * @return mixed
     */
    public  function notify( $var = '');
  
}