<?php
namespace LT\Duoduo\Task;
class NDefaultTaskTimeoutHandler implements ITaskTimeoutHandler{
    private $logger ;
    function __construct( $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function getLogger()
    {
        return is_subclass_of($this->logger,ILogger::class) ? $this->logger : new NLogger() ;
    }

    /**
     * @param mixed $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function processTimeout($context){
        if(isset($context->pid)){
            if(isset( $context->ppid ) && NTaskManager::getPpidWithPid($context->pid) == $context->ppid){
                   $killStatus = posix_kill($context->pid, 9);
                   $this->getLogger()->info ("TaskManager  kill " . $killStatus . "\r\n");
            }
            if(isset($context->traceInfo)){
                $this->getLogger()->notify('taskName ' . $context->taskName .' '. $context->traceInfo );
            }
        }
    }
}