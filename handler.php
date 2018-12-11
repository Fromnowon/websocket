<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/1
 * Time: 14:05
 */
header("Content-type: text/html; charset=utf-8");
include './module/sqlConn.php';
include './module/sqlHandler.php';
$conn = sql_conn("localhost", "root", "8ud7fh", 'my_contest');
$sql_handler = new sqlHandler($conn);

$action = $_GET['action'];
switch ($action) {
    case 'save':
        save_img();
        break;
    case 'create_problem':
        create_problem();
        break;
    case 'p_img_upload':
        p_img_upload();
        break;
    case 'pull_problem':
        pull_problem();
        break;
    case 'pull_problem_selected':
        pull_problem_selected();
        break;
}
//拉取题目列表
function pull_problem_selected()
{
    global $sql_handler;
    $title = $_POST['title'];
    $result = $sql_handler->select('problems', "`title`='$title'");
    echo json_encode($result[0]);
    $sql_handler->close();
}

//拉取标题
function pull_problem()
{
    global $sql_handler;
    $result = $sql_handler->all('problems', 'ORDER by `id` desc');
    echo json_encode($result);
    $sql_handler->close();
}

//编辑器图片上传
function p_img_upload()
{
    $result = array();
    foreach ($_FILES as $file) {
        //遍历每张图
        $arr = explode(".", $file["name"]);
        $type = $arr[count($arr) - 1];//后缀名
        $file_name = md5(uniqid()) . '.' . $type;
        if (!move_uploaded_file($file["tmp_name"], "./userdata/images/" . $file_name)) {
            echo '存储图片时发生错误';
        } else {
            array_push($result, "./userdata/images/" . $file_name);
        }
    }
    echo json_encode($result);
}


//生成题目
function create_problem()
{
    $data = json_decode($_POST['data'], true);
    $date = date('Y-m-d H:i:s');
    global $sql_handler;
    $sql_handler->add('problems', [htmlspecialchars($data['title']), json_encode($data['content'], JSON_UNESCAPED_UNICODE), $date]);
    echo 'SUCCESS';
    $sql_handler->close();
}

//处理图片
function save_img()
{
    $img_data = $_POST['img'];
    $ip = $_POST['ip'];
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_data, $result)) {
        $type = $result[2];
        $new_file = "./img/{$ip}.{$type}";
        if (file_exists($new_file)) {
            //若存在旧文件
            //copy($new_file, "./his_img/" . time() . '.' . $type);
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
