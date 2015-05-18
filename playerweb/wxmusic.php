<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<meta id="view" name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="description" content="" />
		<meta http-equiv="X-UA-Compatible" content="IE=9" />
		<script type="text/javascript" src="js/jquery-2.0.0.min.js"></script>
	</head>
	<body>
		<div class="page">
			<header></header>
			<div class="page_content">
<?php
include_once("base-class.php");


//新建sae类
$mysql = new SaeMysql();


//获取当前页码
$page=intval($_GET["page"]);


//获取url传递过来的ID，删除使用
if($_GET["action"]=="del")
{
	 $id=intval($_GET["id"]);
	 $mysql->runSql("update music set status=0 where mid=$id");

}

?>
                
<div class="home_promo_container" style="padding-bottom:10px;">
<h3 style="font-size:14px;">
音乐管理&nbsp;&nbsp;<a href="wxmusicadd.php">添加音乐>></a>
</h3>
<?
//计算总数
$count=$mysql->getVar("select COUNT(*) from music where status=1 ");
    //每页显示记录数
    $page_num = 5;
    //如果无页码参数则为第一页
    if ($page == 0) $page = 1;
    //计算开始的记录序号
    $from_record = ($page - 1) * $page_num;
	//分页函数
    $multi = multi($count, $page_num, $page, "wxmusic.php");
if($count)
{
    //获取符合条件的数据
    $music_list=$mysql->getData("select *  from  music where status=1 order by mid desc limit $from_record,$page_num");
    foreach($music_list as $value)
    {
        echo "<div class=\"home_promo\" style=\"width:100%;margin-bottom:10px;\">";
        echo "<div class=\"home_promo_content\" style=\"padding-left:10px;\">";
        echo "<h2>{$value[title]}<em>({$value[singer]})</em></h2>";
        echo "<img src='{$value[cover]}' width=100>";
        echo "<p>{$value[intro]}</p>";
        echo "<p>{$value[url]}</p>";
        echo "<p><input onclick=\"wxnews_edit({$value[mid]});\" class=\"get_button\" name=\"get_button\" type=\"button\" value=\"修改\" /><input onclick=\"wxnews_del({$value[mid]});\" class=\"exit_button\" name=\"exit_button\" type=\"button\" value=\"删除\"></p>";
        echo "</div>"; 
        echo "</div>";
    }
}

                    ?>
                    <div class="Pages">
                        <?echo $multi ?> 
                    </div>
                    <div class="clear_both"></div>
                </div>
            </div>
        </div>
	</body>

</html>
<script type="text/javascript">
    //修改
    function wxnews_edit(id){
		location.href = "wxmusicadd.php?id="+id;
	}
    //删除
    function wxnews_del(id){
        if(confirm("确实要删除吗?"))
        {
		location.href = "wxmusic.php?action=del&id="+id;
        }
	}


</script>
