<?php
include_once("base-class.php");

header("Content-type: text/html; charset=utf-8");

//新建sae数据库类
$mysql = new SaeMysql();

//获取当前页码
$page=intval($_GET["page"]);

$mesg = "闪电侠";

//每页显示记录数
$page_num = 10;
//如果无页码参数则为第一页
if ($page == 0) $page = 1;
//计算开始的记录序号
$from_record = ($page - 1) * $page_num;
//获取符合条件的数据
$class_list=$mysql->getData("select *
                from question_tb  
                where question_subject = '$mesg'
                order by question_id desc 
                limit 0,50");

print_r($class_list);
foreach($class_list as $value)
{
	$ost_id = $value[question_id];
	$ost_content = $value[question_options];
	$ost_dl_list = $value[question_true];
                
                    echo "<tr>
                          <td>$ost_id</td>
                          <td>$value[question_subject]</td>
                          <td>$ost_content</td>
                          <td>$ost_dl_list</td>
                          <tr>";
}

?>