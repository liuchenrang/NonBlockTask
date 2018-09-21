<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 15/2/5
 * Time: 下午1:25
 */
namespace LT\Duoduo\Task;
use Exception;
class NBTask  implements ITask, IMultiTask
{

    //该子程进程ID
    protected  $childPid = null;

    //父程进程ID
    protected  $parentPid = null;

    protected $maxProcessCount = 1;

    public function setMaxProcessCount($count){
        $this->maxProcessCount = $count;
    }
    public function getMaxProcessCount(){
        return $this->maxProcessCount ;
    }
    //开启子进程

    /**
     * @param $method
     * @param array $params
     * @param null $ppid
     * @return int
     * @throws Exception
     */
    public function fork($method, array $params=array(), $ppid=null){
        $pid = pcntl_fork();
        $this->parentPid = $ppid;
        if ($pid == -1) {
            throw new Exception ('fork error on Task object');
        } elseif ($pid) {
            # we are in parent class
            $this->childPid = $pid;
            return $pid;
        } else{
            $this->parentPid = is_null($ppid) ? posix_getppid() : $ppid;
            $this->childPid  = function_exists('posix_getpid') ? posix_getpid() : getmypid();
            //echo "A = ppid:".self::$ppid." pid:".self::$pid."\n";
            //调用子进程方法
            $maxCount = $this->getMaxProcessCount();
            for ($i=0; $i < $maxCount; $i++) { 
                call_user_func_array(array($this,$method), $params);
            }
            exit(0); //显式调用中断
        }
    }



    /**
     * @return null
     */
    public function getChildPid()
    {
        return $this->childPid;
    }

    /**
     * @param null $childPid
     */
    public function setChildPid($childPid)
    {
        $this->childPid = $childPid;
    }

    /**
     * @return null
     */
    public function getParentPid()
    {
        return $this->parentPid;
    }

    /**
     * @param null $parentPid
     */
    public function setParentPid($parentPid)
    {
        $this->parentPid = $parentPid;
    }//END func fork




    //结束
    public function finish(){
        //do sth.
        exit(0);
    }//END func finish
  
}