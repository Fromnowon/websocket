<?php

//第一种方法，执行外部cmd命令，待测
class GetHostIP
{
    public function byShell()
    {
        $out = shell_exec('ipconfig');//win系统下原编码是gbk
        $out = mb_convert_encoding($out, 'UTF-8', 'GBK');
        preg_match_all("/IPv4 地址 . . . . . . . . . . . . : (\d+\.\d+\.\d+\.\d+)/", $out, $match1);
        preg_match_all("/默认网关. . . . . . . . . . . . . : (\d+\.\d+\.\d+\.\d+)/", $out, $match2);
        $gateway = $match2[1][0];
        $gateway_str = substr($gateway, 0, strrpos($gateway, '.'));
        $host_ip = null;
        foreach ($match1[1] as $key => $ip) {
            //判断ip是否在网关段上(如192.168.1.1与192.168.1.111的前三段一致)
            $ip_str = substr($ip, 0, strrpos($ip, '.'));
            if ($ip_str == $gateway_str) {
                $host_ip = $ip;
            }
        }
        return $host_ip;
    }
}
