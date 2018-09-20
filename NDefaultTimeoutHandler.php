<?php
namespace LT\Duoduo\Task;
class NDefaultTimeoutHandler implements ITimeoutHandler{

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