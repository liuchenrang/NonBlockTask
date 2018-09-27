<?php
/**
 * Created by PhpStorm.
 * User: chen
 * Date: 2018/9/21
 * Time: 下午3:16
 */

namespace LT\Duoduo\Task;

class NPStackTraceHandler extends NBaseTraceHandler
{
  
    public function isSupport()
    {
        $execOutput = [];
        exec('whereis pstack', $execOutput, $execStatus);
        return count($execOutput) >= 1 && strpos($execOutput[0], "pstack") !== false;
    }
    public function trace($pidInfo, $statInfo)
    {
        $pid = $pidInfo['pid'];
        $output = PHP_EOL . "pstack $pid " . PHP_EOL;
        if ($this->isSupport() && $pid) {
            $traceCmd = "pstack $pid";
            $this->manager->info("TaskManager timeout trigger parse! pstackCmd $traceCmd \r\n");
            try {
                $output = $this->execWithTimeout($traceCmd, $this->manager->getParseExetime());
            } catch (\Exception $e) {
                $output = $e->getMessage();
            }
        }
        return $output;

    }
}
