<!DOCTYPE HTML>
<html>
<HEAD>
<meta charset="utf-8">
<TITLE>‘今日导视’网页链接管理</TITLE>
</HEAD>
<body>
    
<?php
include_once("base-class.php");

//新建sae数据库类
$mysql = new SaeMysql();

//获取当前页码
$page=intval($_GET["page"]);

//获取操作标识传入
$action=$_GET["action"];
$action= string::un_script_code($action);
$action= string::un_html($action);


//是否删除
if($action=="del")
{
    //获取今日导视ID号传入
    $jrds_id=intval($_GET["jrds_id"]);
    //获取当前时间
    $nowtime=date("Y/m/d H:i:s",time());
	$mysql->runSql("update jrds set status=0 where jrds_id=$jrds_id");    
    echo "<script>alert('操作成功！');location='jrds_manager.php?page=$page';</Script>";
    exit;    
}    
//列表数据获取、分页

//计算总数
$count=$mysql->getVar("select COUNT(*) from jrds_tb where status=1");
//如果数据表里有数据
if($count)
{
    //每页显示记录数
    $page_num = 10;
    //如果无页码参数则为第一页
    if ($page == 0) $page = 1;
    //计算开始的记录序号
    $from_record = ($page - 1) * $page_num;
    //获取符合条件的数据
    $class_list=$mysql->getData("select *
                from jrds_tb  
                order by jrds_id desc 
                limit $from_record,$page_num");
    //分页函数
    $multi = multi($count, $page_num, $page, "jrds_manager.php");
}
?>
    <!--页面名称-->
	<h3>‘今日导视’网页链接管理<a href="jrds_add.php">添加新的‘今日导视’网页链接>></a></h3>
    <!--列表开始-->
    
    <table border=1>
        <tr>
            <td>序号</td><td>'今日导视'标题名称</td><td>'今日导视'网页链接</td><td>'今日导视'描述</td><td>操作</td>
        </tr>
        <?php
			if($class_list)
            {
                foreach($class_list as $value)
                {
                
                    echo "<tr>
                          <td>$value[jrds_id]</td>
                          <td>$value[jrds_subject]</td>
                          <td>$value[jrds_options]</td>
                          <td>$value[jrds_true]</td>
                          <td>
                            <a href='jrds_manager.php?action=del&jrds_id=$value[jrds_id]'>删除</a>
                            <a href='jrds_add.php?jrds_id=$value[jrds_id]'>修改</a>
                          </td>
                          <tr>";
                }
            }
			else
            {
                echo "<tr><td colspan=4>无记录</td></tr>";
            }
        ?>
    
    </table>
    <?php
	echo $multi;
    ?>
</body>
</html>
