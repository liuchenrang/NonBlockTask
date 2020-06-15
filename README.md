
```shell
快速集成
采用 NTaskManager
//方法
        NTaskManager::defaultManger(new NLogger())
            ->setProgramNum(5)
            ->setDebug(true)
            ->addMultiTask($threadNum,__CLASS__, $method, array('i' => 0))
            ->run();
//支持钉钉报警
$logger = new NDingDingLogger("https://oapi.dingtalk.com/robot/send?access_token=xx");
ztm->setTaskTimeoutHandler(new NDefaultTaskTimeoutHandler($logger));


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
//自定义超时确认机制
$ztm->setConfirmDieActionHandler(new NDefaultConfirmDieAction(10));



//设置一个进程执行多少次后， 退出，新建进程; 设置子进程执行次数
$ztm->setMaxProcessCount(30) 
for ($i = $params['i']; $i < $threadNum; $i++) {
    $ztm->add(__CLASS__, $method, (array($i)));
}

//简版创建多个进程方法
$ztm->addMultiTask($threadNum,__CLASS__, $method, array('i' => 0) );

$ztm->run();



/**
 * Interface IConfirmDieAction
 * @package LT\Duoduo\Task
 * @author  duoduo
 */
interface IConfirmDieAction{
    /**
     * @param array $stats
     * 数组类型， 元素是一个NStatContext ，统计回收数据，评估结束时间
     * @return mixed
     */
    public function stats($stats);

    /**
     * @param $data
     * 元素是一个NStatContext 返回任务任务已经挂掉的时间单位秒
     * @return mixed
     */
    public function getDieTimeout($data);
}
NDefaultConfirmDieAction 默认实现类 返回一个构造器的时间

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

NDefaultTaskTimeoutHandler 默认实现类， 调用NLogger 的notify接口 


//demo
use LT\Duoduo\Task\NTaskManager;
use LT\Duoduo\Task\NDefaultTaskTimeoutHandler;
use LT\Duoduo\Task\NLogger;


class DuoduoTask extends BaseTask
{
    public function importAction()
    {
        $method = 'processOrder';
        $threadNum = 2;
        $ztm = new NTaskManager();
        $ztm->setTaskTimeoutHandler(new NDefaultTaskTimeoutHandler(new NLogger()));
        $ztm->setLogger(new NLogger());
        $ztm->setDebug(true);
        $ztm->addMultiTask($threadNum,__CLASS__, $method, array('i' => 0) );
        $ztm->setStatsWhenReCreateTask(true);
        $ztm->setTrace(true);
        $ztm->setParseExetime(10);
        $ztm->setDaemon(1);
        $ztm->run();

        NTaskManager::defaultManger(new NLogger())
            ->setProgramNum(5)
            ->setDebug(true)
            ->addMultiTask($threadNum,__CLASS__, $method, array('i' => 0))
            ->run();

    }



    public function processOrder($i){
        $sleep = 0;
        if($i % 3 == 1){
            $sleep = 10;
        }
        $pid  = $this->getpid();
        file_put_contents("/tmp/run.log", ("processOrder sleep {$sleep}  pid $pid end " . $i . "\r\n"), FILE_APPEND);

    }
    public function socket(){
        $fp = fsockopen("tcp://redis", 6379, $errno, $errstr);
        if (!$fp) {
            echo "ERROR: $errno - $errstr<br />\n";
        } else {
            fwrite($fp, "\n");
            echo fread($fp, 1026);
            // fclose($fp);
        }
    }
}

```