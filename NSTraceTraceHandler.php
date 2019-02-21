<?php

/**
 * Created by PhpStorm.
 * User: chen
 * Date: 2018/9/21
 * Time: 下午3:16
 */

namespace LT\Duoduo\Task;
use Exception;
class NSTraceTraceHandler extends NBaseTraceHandler
{
     private $support = false;
    public function __construct($manger){
            parent::__construct($manger);
            $this->support = $this->checkSupport();
    }
    public function isSupport()
    {
       return $this->support;
    }
    public function checkSupport()
    {
        $execOutput = [];
        exec('strace -V', $execOutput, $execStatus);
        return count($execOutput) >= 1 && strpos($execOutput[0], "strace") !== false;
    }
    public function trace($pidInfo, $statInfo)
    {
        $pid = $pidInfo['pid'];
        $traceCmd = "strace -p $pid";
        $this->manager->info("TaskManager timeout trigger parse! traceCmd $traceCmd \r\n");
        try {
            $output = $this->execWithTimeout($traceCmd, $this->manager->getParseExetime());
        } catch (Exception $e) {
            $output = $e->getMessage();
        }
        return $output;
    }
}
