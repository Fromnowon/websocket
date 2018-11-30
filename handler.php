<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/1
 * Time: 14:05
 */
header("Content-type: text/html; charset=utf-8");
$action = $_GET['action'];
$img_data = $_POST['img'];
if ($action == 'save') {
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_data, $result)) {
        $type = $result[2];
        $new_file = "./img/pic.{$type}";
        if (file_exists($new_file)) {
            //若存在旧文件
            copy($new_file, "./his_img/" . time() . '.' . $type);
            unlink($new_file);//删除旧文件
        }
        if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $img_data)))) {
            //开始调用py
            //$output = shell_exec("python python/recognition.py recognition");
            //返回图片地址
            echo $new_file;
        }
    }
}
