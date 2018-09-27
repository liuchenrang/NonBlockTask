<?php
/**
 * Created by PhpStorm.
 * User: chen
 * Date: 2018/9/21
 * Time: 下午3:16
 */

namespace LT\Duoduo\Task;

class NPHPTraceTraceHandler extends NBaseTraceHandler
{
    public function isSupport()
    {
        $execOutput = [];
        exec('phptrace -h', $execOutput, $execStatus);
        return count($execOutput) >= 1 && strpos($execOutput[0], "usage:") !== false;
    }
    public function trace($pidInfo, $statInfo)
    {
        $pid = $pidInfo['pid'];
        $taskName = $statInfo->taskName;
        $traceCmd = "phptrace -p $pid -o /tmp/phptrace.$taskName.$pid.log";
        $this->manager->info("TaskManager timeout trigger parse! traceCmd $traceCmd \r\n");
        try {
            $output = $this->execWithTimeout($traceCmd, $this->manager->getParseExetime());
        } catch (\Exception $e) {
            $output = $e->getMessage();
        }
        return $output;

    }
}
