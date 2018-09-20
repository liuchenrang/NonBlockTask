<?php
namespace LT\Duoduo\Task;
interface IConfirmDieAction{
    public function stats($stats);
    public function getDieTimeout($data);
}