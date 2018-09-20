```shell
软重启
kill -1 pid
//使用方式 
$ztm = new NTaskManager();
//检测到超时时，怎么处理
$ztm->setTimeoutHandler(new NDefaultTimeoutHandler()); 
//日志怎么记录
$ztm->setLogger(new Logger());
//详细的运行日志
$ztm->setDebug(true);
//deamon 模式下完成一圈任务后进行stats 统计
$ztm->setStatsWhenReCreateTask(true);
//开启追踪进程信息 在任务规定的时间内没有完成时开始。
$ztm->setTrace(true);
$ztm->setParseExetime(10); //开启收集后的收集执行时间
$ztm->setTaskTimeoutHandler()

for ($i = $params['i']; $i < $threadNum; $i++) {
    $ztm->add(__CLASS__, $method, (array($i)));
}
$ztm->setDaemon(1);
$ztm->run();
```