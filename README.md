```shell
软重启
kill -1 pid
//使用方式 
$ztm = new NTaskManager();
//deamon 模式下完成一圈任务后进行stats 统计
$ztm->setStatsWhenReCreateTask(true);
//检测到超时时，怎么处理
$ztm->setTaskTimeoutHandler(new NDefaultTaskTimeoutHandler());


//开启守护模式
$ztm->setDaemon(1);
 //开启追踪进程信息 在任务规定的时间内没有完成时开始。
 $ztm->setTrace(true);
 //详细的运行日志
 $ztm->setDebug(true);
//日志怎么记录
$ztm->setLogger(new Logger());

//开启strace后，收集执行时间
$ztm->setParseExetime(10)


$ztm->setConfirmDieActionHandler(new NDefaultConfirmDieAction());



//设置一个进程执行多少次后， 退出，新建进程; 设置子进程执行次数
$ztm->setMaxProcessCount(30) 
for ($i = $params['i']; $i < $threadNum; $i++) {
    $ztm->add(__CLASS__, $method, (array($i)));
}
$ztm->run();
```