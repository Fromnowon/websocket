<?php

function sql_conn($host, $user, $psw, $table)
{
    //$conn = mysqli_connect("localhost", "root", "8ud7fh") or die("连接数据库失败" . mysqli_error($conn));
    //$conn = mysqli_connect("localhost", "root", "8ud7fh") or die("连接数据库失败" . mysqli_error($conn));
    $conn = mysqli_connect($host, $user, $psw) or die("连接数据库失败" . mysqli_error($conn));
    mysqli_select_db($conn, $table) or die("数据库访问错误" . mysqli_error($conn));

    error_reporting(0);
    mysqli_query($conn, "set names 'utf8'");
    ini_set('date.timezone', 'Asia/Shanghai');
    return $conn;
}