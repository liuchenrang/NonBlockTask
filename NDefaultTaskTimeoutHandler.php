<?php
namespace LT\Duoduo\Task;
class NDefaultTaskTimeoutHandler implements ITaskTimeoutHandler{
    private $logger ;
    function __construct( ILogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param mixed $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function processTimeout($context){
        $execOutput = [];
        if(isset($context->pid)){
            if(isset( $context->ppid ) && NTaskManager::getPpidWithPid($context->pid) == $context->ppid){
                   $killStatus = posix_kill($context->pid, 9);
                   echo ("TaskManager  kill " . $killStatus . "\r\n");
            }
            if(isset($context->traceInfo)){
                echo $context->traceInfo;
            }
        }
    }
}