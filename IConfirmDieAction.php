<?php
namespace LT\Duoduo\Task;
/**
 * Interface IConfirmDieAction
 * @package LT\Duoduo\Task
 * @author  duoduo
 */
interface IConfirmDieAction{
    /**
     * @param array $stats
     * 数组类型， 元素是一个NStatContext ，统计回收数据，评估结束时间
     * @return mixed
     */
    public function stats($stats);

    /**
     * @param $data
     * 元素是一个NStatContext 返回任务任务已经挂掉的时间单位秒
     * @return mixed
     */
    public function getDieTimeout($data);
}