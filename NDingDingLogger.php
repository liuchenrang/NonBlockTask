<?php
/**
 * Created by PhpStorm.
 * User: chen
 * Date: 2018/9/21
 * Time: 下午3:16
 */

namespace LT\Duoduo\Task;


class NDingDingLogger extends NLogger
{
    private $robotUrl ;
    public function __construct($robotUrl)
    {
        $this->robotUrl = $robotUrl;
    }

    public function notify($content = '')
    {
        $this->voiceBoot($this->robotUrl, [
            'msgtype' => "text",
            "at" => [
//                "isAtAll" => $atAll,
##              "atMobiles" => $atPeople
            ],
            'text' => ['content' => "任务超时" . " \r\n " . $content . " "],
        ]);

    }
    function voiceBoot($url, $vars)
    {

        $data_string = json_encode($vars);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $headers = [
            'Accept:application/json',
            'Content-Type: application/json;charset=utf-8',
            'Content-Length: ' . strlen($data_string),
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $server_output = curl_exec($ch);
        var_dump($server_output);

        $data = json_decode($server_output, 1);
        curl_close($ch);
        return $data;
    }

}