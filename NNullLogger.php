<?php
/**
 * Created by PhpStorm.
 * User: chen
 * Date: 2018/9/21
 * Time: 上午11:11
 */

namespace LT\Duoduo\Task;


class NNullLogger implements ILogger
{
    public function debug($var = '')
    {
    }

    public function notify($var = '')
    {
    }
}