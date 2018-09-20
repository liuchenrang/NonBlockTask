<?php
namespace LT\Duoduo\Task;
interface IConfirmDieTimeout{
    public function stats($stats);
    public function getDieTimeout($data);
}