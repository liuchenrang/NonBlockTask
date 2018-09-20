<?php
namespace LT\Duoduo\Task;
class NDefaultConfirmDieTimeout implements IConfirmDieTimeout{
    private $timeout = 0;
    public function __construct($timeout=0){
        $this->timeout = $timeout;
    }
    public function stats($stats){
        
        echo sprintf("%7s,%25s,%5s s ".PHP_EOL,'TaskId', '任务名称', '运行时间');
        foreach($stats as $taskId => $info){
            if($info->stopTime > 0){
                echo sprintf("%7s,%25s,%5s s".PHP_EOL,$taskId, $info->taskName, $info->stopTime - $info->startTime);
            }
        }


    }
    public function getDieTimeout($data){
        return $this->timeout;
    }
}