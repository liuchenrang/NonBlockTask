<?php

namespace LT\Duoduo\Task;

if (!function_exists('pcntl_fork')) {
    die('PCNTL functions not available on this PHP installation');
}

use Exception;

/**
 * Class NTaskManager
 * 任务管理
 * @package LT\Duoduo\Task
 */
class NTaskManager
{

    //进程池
    protected $taskPool = array();

    protected $pid = null;

    private $taskTimeoutHandler;
    private $childrenPIDInfo = [];
    private $childrenStatInfo = [];
    private $debug = false;
    private $confirmDieActionHandler;
    private $parseExetime = 5;
    private $strace = false;
    private $pstack = false;
    private $maxProcessCount = 1;
    private $taskId = 0;
    private $statsWhenReCreateTask = false;
    private $logger;
    private $defaultDieTime = 5;
    private $daemon = 0;
    private $programNum = 0;
    private $nbTask;

    private $traceHelper = [];

        //初始化
    public function __construct()
    {
        $this->pid = function_exists('posix_getpid') ? posix_getpid() : getmypid();
        $this->nbTask = new NBTask();
        $this->traceHelper[] = new NSTraceTraceHandler($this);
        $this->traceHelper[] = new NPStackTraceHandler($this);
        $this->traceHelper[] = new NPHPTraceTraceHandler($this);
    }

    /**
     * @return bool
     */
    public function isPstack()
    {
        return $this->pstack;
    }

    /**
     * @param bool $pstack
     */
    public function setPstack($pstack)
    {
        $this->pstack = $pstack;
        return $this;
    }

    /**
     * @return int
     */
    public function getProgramNum()
    {
        return $this->programNum;
    }

    /**
     * @param int $programNum
     */
    public function setProgramNum($programNum)
    {
        $this->programNum = $programNum;
        return $this;

    }

    /**
     * 批量添加任务
     *
     * @param $programCount
     * 类目
     * @param $className
     * @param $method
     * @param $params
     */
    public function addMultiTask($programCount, $className, $method, $params)
    {
        $this->setProgramNum($programCount);
        $start = $params['i'];
        for ($i = $start; $i < $this->getProgramNum(); $i++) {
            $this->add($className, $method, (array($i)));
        }
        return $this;
    }

    public function addMultiCallableTask($programCount, $callable, $params)
    {
        $this->setProgramNum($programCount);
        for ($i = $start; $i < $this->getProgramNum(); $i++) {
           $result = $this->addCallable($callable, $params);
           if(!$result){
               throw new \Exception("add callable fail!");
           }
        }
        return $this;
    }
    public function setStatsWhenReCreateTask($bool)
    {
        $this->statsWhenReCreateTask = $bool;
        return $this;

    }

    public function getStatsWhenReCreateTask()
    {
        return $this->statsWhenReCreateTask;
    }

    public function setTaskTimeoutHandler($handler)
    {
        $this->taskTimeoutHandler = $handler; //handler
        return $this;

    }

    public function setDaemon($bool)
    {
        $this->daemon = $bool;
        return $this;

    }

    /**
     * 是否开启跟踪
     * @param boolean $bool
     */
    public function setTrace($bool)
    {
        $this->strace = $bool;
        return $this;

    }

    public function setDebug($bool)
    {
        $this->debug = $bool;
        return $this;

    }

    public function isStrace()
    {
        return $this->strace;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;

    }

    public function setParseExetime($time)
    {
        $this->parseExetime = $time;
        return $this;

    }

    public function getParseExetime()
    {
        return $this->parseExetime;
    }



    public function setConfirmDieActionHandler($timeout)
    {
        $this->confirmDieActionHandler = $timeout;
        return $this;

    }

    public static function defaultManger($logger, $dieTime = 30)
    {
        $ztm = new NTaskManager();
        $ztm->setTaskTimeoutHandler(new NDefaultTaskTimeoutHandler($logger));
        $ztm->setLogger($logger);
        $ztm->setConfirmDieActionHandler(new NDefaultConfirmDieAction($dieTime));
        $ztm->setStatsWhenReCreateTask(true);
        $ztm->setTrace(true);
        $ztm->setDaemon(1);
        $ztm->setParseExetime(5);
        return $ztm;
    }

    public function getConfirmDieActionHandler()
    {
        return is_subclass_of($this->confirmDieActionHandler, IConfirmDieAction::class) ? $this->confirmDieActionHandler : new NDefaultConfirmDieAction($this->defaultDieTime);
    }

    public static function getChildPidByPpid($ppid)
    {
        $cmd = "ps -ef|grep -v ef|awk '$3=={$ppid}{print $2}'";
        exec($cmd, $execOutput, $status);
        if (isset($execOutput[0])) {
            return $execOutput;
        } else {
            return [];
        }
    }

    public static function getChildPidCountByPpid($ppid)
    {
        $cmd = "ps -ef|awk '$3=={$ppid}{print $3}'|wc -l";
        exec($cmd, $execOutput, $status);
        if (isset($execOutput[0])) {
            return intval($execOutput[0]) - 1;
        } else {
            return 0;
        }
    }
    public function addCallable($callable, $params){
           //校验method是否存在
        if (is_callable($callable, false) ) {
            $taskId = ++$this->taskId;
            $this->taskPool[$taskId] = array($callable, $params);
            return true;
        }
        return false;
    }

    public static function getPpidWithPid($pid)
    {
        $cmd = "ps -ef|awk '$2=={$pid}{print $3}'";
        exec($cmd, $execOutput, $status);
        if (isset($execOutput[0])) {
            return isset($execOutput[0]);
        } else {
            return 0;
        }
    }

    public function execWithTimeout($cmd, $timeout)
    {
        // File descriptors passed to the process.
        $descriptors = array(
            0 => array('pipe', 'r'), // stdin
            1 => array('pipe', 'w'), // stdout
            2 => array('pipe', 'w'), // stderr
        );

        // Start the process.
        $process = proc_open('exec ' . $cmd, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new Exception('Could not execute process');
        }

        // Set the stdout stream to none-blocking.
        stream_set_blocking($pipes[0], 0);
        stream_set_blocking($pipes[1], 0);
        stream_set_blocking($pipes[2], 0);
        // Turn the timeout into microseconds.
        $timeout = $timeout * 1000000;

        // Output buffer.
        $buffer = '';
        $errors = '';

        // While we have time to wait.
        try {
            while ($timeout > 0) {
                $start = microtime(true);

                // Wait until we have output or the timer expired.
                $read = array($pipes[1]);
                $other = array();
                stream_select($read, $other, $other, 0, $timeout);
                // Get the status of the process.
                // Do this before we read from the stream,
                // this way we can't lose the last bit of output if the process dies between these     functions.
                $status = proc_get_status($process);

                // Read the contents from the buffer.
                // This function will always return immediately as the stream is none-blocking.
                $buffer .= stream_get_contents($pipes[1]);

                if (!$status['running']) {
                    // Break from this loop if the process exited before the timeout.
                    break;
                }
                $errors .= stream_get_contents($pipes[2]);

                // Subtract the number of microseconds that we waited.
                $timeout -= (microtime(true) - $start) * 1000000;
            }
            // Check if there were any errors.

            if (!empty($errors)) {
                $errors = ($buffer . $errors);
                throw new Exception($errors);
            }
        } finally {

            // Kill the process in case the timeout expired and it's still running.
            // If the process already exited this won't do anything.

            proc_terminate($process, 9);
            // Close all streams.
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            proc_close($process);

        }
        return $buffer;
    }

    //添加进程至控制器
    //如果添加失败 则返回false
    public function add($task, $method, array $params = array())
    {
        //校验method是否存在
        if (method_exists($task, $method)) {
            $taskId = ++$this->taskId;
            $this->taskPool[$taskId] = array($task, $method, $params);
            return true;
        }
        return false;
    } //END func add

    public function info($info)
    {
        if ($this->debug && is_subclass_of($this->logger, ILogger::class)) {
            $this->logger->debug($info);
        }
    }

    public function setMaxProcessCount($count)
    {
        $this->maxProcessCount = $count;
        return $this;

    }

    public function getMaxProcessCount()
    {
        return $this->maxProcessCount;
    }

    private function createSingleTaskByTaskId($taskId)
    {
        if (!isset($this->taskPool[$taskId])) {
            return false;
        }
        $taskInfo = $this->taskPool[$taskId];
        $taskType = count($taskInfo) == 3 ? 1 : 2;
        if($taskType == 1){
            list($className, $method, $params) = $taskInfo;
            $task = $this->nbTask;
            if (is_subclass_of($task, IMultiTask::class)) {
                $task->setMaxProcessCount($this->getMaxProcessCount());
            }
            $pid = $task->fork(new $className, $method, $params, $this->pid); //ZTask 类实现
        } else {
            list($method, $params) = $taskInfo;
            $task = $this->nbTask;
            $task->setMaxProcessCount($this->getMaxProcessCount());
            $pid = $task->forkCallable($method, $params, $this->pid); //ZTask 类实现
        }
        
        $time = time();
        $this->info("TaskManager 1 ppid {$this->pid} child  pid  " . $pid);
        $this->childrenPIDInfo[$taskId] = [
            'pid' => $pid,
            'ppid' => $this->pid,
            'task' => $task,
            'startTime' => $time,
        ];
        $stat = new NStatContext();
        $stat->taskName = $taskType == 1 ? $className . '::' . $method : "callable::".$taskId;
        $stat->startTime = $time;
        $stat->stopTime = 0;
        $stat->pid = $pid;
        $this->childrenStatInfo[$taskId] = $stat;
        return true;
    }

    private function createTask()
    {
        $this->childrenPIDInfo = [];
        $this->childrenStatInfo = [];
        $taskIdList = array_keys($this->taskPool);
        foreach ($taskIdList as $taskId) {
            $this->createSingleTaskByTaskId($taskId);
        }
    }

    //开启进程列表
    public function run()
    {

        if ($this->daemon <= 0) {
            $this->info("TaskManager create task ");
            $this->createTask();
            $this->info("TaskManager fork end ");
            $this->wait();
            $this->info("TaskManager finish end ");
        } else {

            $this->daemon();
        }

    } //END func run

    public function daemon()
    {
        $this->daemon = 1;
        pcntl_signal(SIGHUP, function () {
            $this->daemon = 0;
            echo 'SIGHUP  !' . PHP_EOL;
        }, false);
        $this->createTask();
        while ($this->daemon) {
            pcntl_signal_dispatch();
            $childrenPids = $this->childrenPIDInfo;
            $this->info("TaskManager daemon  watchRun ing ");
            $keepAliveList = self::getChildPidByPpid($this->pid); //这里必须放在watchRuning 前面， 保证keepaliveList集合大于，当前运行的集合。
            $this->watchRuning($childrenPids);
            $this->checkHealth($keepAliveList); //移除退出信息后， 进程快照和内存快照是否一致！

            $stopTasks = $this->getStopTasksId();
            $this->info("TaskManager stop task id " . json_encode($stopTasks));
            $this->startStopping($stopTasks);
            // exit;
            sleep(1);
        }

    }

    private function checkHealth($keepaliveList)
    {

        foreach ($this->childrenPIDInfo as $pidInfo) {
            if (is_array($pidInfo) && isset($pidInfo['pid'])) {
                if (!in_array($pidInfo['pid'], $keepaliveList)) {
                    $info = "TaskManager task bad! memory pid " . json_encode($this->arrayGet($this->childrenPIDInfo, 'pid')) . ' ps pid ' . json_encode($keepaliveList);
                    $this->info($info);
                    throw new Exception($info);
                }
                $this->info("TaskManager checkHealth " . $pidInfo['pid']);

            }
            // var_dump($keepaliveList, $pidInfo);exit;
        }
        if (count($this->childrenPIDInfo) > $this->getProgramNum()) {
            throw new Exception("TaskManager child  more than keep ");
        }
    }

    private function arrayGet($arr, $valueKey)
    {
        $newArr = [];
        foreach ($arr as $value) {
            $newArr[] = $value[$valueKey];
        }
        return $newArr;
    }

    private function startStopping($stopTasks)
    {
        if (is_array($stopTasks)) {
            foreach ($stopTasks as $taskId) {
                $result = $this->createSingleTaskByTaskId($taskId);
                $this->info("TaskManager restart result " . intval($result));
            }
        } else {
            $this->info("TaskManager restart Task list empty!");
        }
    }

    private function getStopTasksId()
    {
        $runingTask = array_keys($this->childrenPIDInfo);
        $allTask = array_keys($this->taskPool);
        $stopTask = array_diff($allTask, $runingTask);
        return $stopTask;
    }

    public function finish($pid)
    {

    }

    public function handlerFinishTask($taskId)
    {
        $this->childrenStatInfo[$taskId]->stopTime = time();
        if (isset($this->childrenPIDInfo[$taskId])) {
            $this->finish($this->childrenPIDInfo[$taskId]['pid']);
        } else {
            return false;
        }

    }

    public function getChildrenStatInfo()
    {
        return $this->childrenStatInfo;
    }

    public function stats()
    {
        $stats = $this->getChildrenStatInfo();
        $this->getconfirmDieActionHandler()->stats($stats);

    }

    private function handlerTaskExit($taskId)
    {
        if ($this->daemon) {

            if ($this->getStatsWhenReCreateTask()) {
                $this->stats();
            }

        }
    }

    private function watchRuning($childrenPids)
    {
        foreach ($childrenPids as $taskId => $pidInfo) {
//            if (!isset($pidInfo['pid'])) continue;
            $pid = $pidInfo['pid'];
            $res = pcntl_waitpid($pid, $status, WNOHANG);
            // If the process has already exited
            if ($res == -1 || $res > 0) {
                try {
                    $this->handlerFinishTask($taskId);
                    $this->info("TaskManager ExitTaskId $taskId pid $pid \r\n");
                    $this->handlerTaskExit($taskId);
                } finally {
                    $this->removeRunTask($taskId);
                }
            } else {
                if ($this->isStrace()) {
                    $statInfo = $this->childrenStatInfo[$taskId];
                    $task = $this->nbTask;
                    $task->setMaxProcessCount($this->getMaxProcessCount());
                    $this->handlerStrace($pidInfo, $statInfo);                        

                   
                }
            }
        }
        $this->info("TaskManager next loop check \r\n");
    }

    private function removeRunTask($taskId)
    {
        unset($this->childrenPIDInfo[$taskId]);
    }

    //确认结束子进程
    protected function wait()
    {
        do {
            $childrenPids = $this->childrenPIDInfo;
            $this->watchRuning($childrenPids);
            sleep(1);
        } while (count($childrenPids));

    } //END func finish
    private function doTrace($pidInfo, $statInfo){
            $traceInfo = '';
            if (is_array($this->traceHelper)) {
                foreach ($this->traceHelper as $trace) {
                    if ($trace->isSupport()) {
                        $traceInfo .= $trace->trace($pidInfo, $statInfo);
                    }
                }
                $call = $this->taskTimeoutHandler;
                if (is_subclass_of($call, ITaskTimeoutHandler::class)) {
                    $context = new NContext();
                    $context->pid = $pid;
                    $context->ppid = $pidInfo['ppid'];
                    $context->taskName = $statInfo->taskName;
                    $context->traceInfo = $traceInfo;
                    $call->processTimeout($context);
                } else {
                    $this->info('ITaskTimeoutHandler is not\r\n');
                }
                $this->info("traceOutput:" . $traceInfo . '\r\n');
            }
    }
    /** 
     * @param $pidInfo
     * @param $statInfo
     * @throws Exception
     */
    private function handlerStrace($pidInfo, $statInfo)
    {
        if (!isset($pidInfo['pid'])) {
            throw new Exception("handler strace pidinfo not include pid!");
        }
        $pid = $pidInfo['pid'];
        $now = time();
        $defaultDieTime = $this->getConfirmDieActionHandler()->getDieTimeout($statInfo);
        $aliveExpireTime = $pidInfo['startTime'] + $defaultDieTime;
        $latestPidInfo = $this->childrenPIDInfo[$pid];
        if ($aliveExpireTime < $now && !isset($latestPidInfo["isDoTrrace"])) {
            $pid = $task->forkCallable(function()use($pidInfo, $statInfo){
                $this->doTrace($pidInfo, $statInfo);
            }, [], $this->pid); //ZTask 类实现
            $this->childrenPIDInfo[$pid]["isDoTrrace"] = true;
        }
        $this->info("TaskManager check child status $pid startTime {$pidInfo['startTime']} aliveExpireTime $aliveExpireTime defaultDieTime $defaultDieTime  now $now\r\n");
        sleep(1);
    }

} //END class ZTaskManager
