<?php
namespace LT\Duoduo\Task;
interface IMultiTask{
    /**
     * @return mixed
     * 多次运行当个任务的最大次数
     */
    function getMaxProcessCount();
}