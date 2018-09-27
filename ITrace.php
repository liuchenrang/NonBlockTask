<?php
namespace LT\Duoduo\Task;
/**
 * @author duoduo
 */
interface ITrace
{
    function isSupport();
    function trace($pidInfo,$statInfo);
}
