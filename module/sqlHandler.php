<?php
/**
 * Created by PhpStorm.
 * User: ZZH
 * Date: 2018/12/3
 * Time: 10:50
 */

class sqlHandler
{
    private $conn = null;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    //--------------------PHP TOOL------------------------//
    function select($table, $filter)
    {
        //条件需手动拼凑成sql语句
        $sql = "select * from `$table` where $filter";
        $res = mysqli_query($this->conn, $sql);
        $arr = array();
        while ($row = mysqli_fetch_assoc($res)) {
            $arr[] = $row;
        }
        //注意，返回的结果为二维数组
        return $arr;
    }

    function all($table, $order)
    {
        $sql = "select * from  `$table` " . $order;
        $res = mysqli_query($this->conn, $sql);
        $arr = array();
        while ($row = mysqli_fetch_assoc($res)) {
            $arr[] = $row;
        }
        return $arr;
    }

    function add($table, $arr)
    {
        //arr需写成数组形式，且必须按顺序
        //mysqli_affected_rows返回最近一次sql操作影响的行数
        $str = array_values($arr);
        $str = implode("','", $str);
        $sql = "insert into `$table` values(DEFAULT ,'$str')";
        $res = mysqli_query($this->conn, $sql);
        if (!$res) {
            echo __FUNCTION__ . '执行失败！' . $sql . "\n";
        }
    }

    function delete($table, $where)
    {
        $sql = "delete from `$table` where $where";
        $res = mysqli_query($this->conn, $sql);
        if (!$res) {
            echo __FUNCTION__ . '执行失败！' . $sql . "\n";
        }
    }

    function update($table, $update, $where)
    {
        $sql = "update `$table` set $update where $where";
        $res = mysqli_query($this->conn, $sql);
        if (!$res) {
            echo __FUNCTION__ . '执行失败！' . $sql . "\n";
        }
    }
}
