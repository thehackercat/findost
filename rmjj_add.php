<!DOCTYPE HTML>
<html>
<HEAD>
<meta charset="utf-8">
<TITLE>‘热门剧集’网页链接添加/修改</TITLE>
</HEAD>
<body>
    
<?php
include_once("base-class.php");

//新建sae数据库类
$mysql = new SaeMysql();

//获取热门剧集外链ID号传入
$rmjj_id=intval($_GET["rmjj_id"]);

//获取操作标识传入
$action=$_POST["action"];
$action= string::un_script_code($action);
$action= string::un_html($action);

//判断是否修改，如果传入了问题ID，进行数据库查询获取全部内容
if($rmjj_id)
{
	$rmjj_value=$mysql->getLine("select * from rmjj_tb where rmjj_id=$rmjj_id");
    if(!$rmjj_value)
	{
		echo "<script>alert('无此<热门剧集>网页链接');history.back();</Script>";
		exit;
	}
}

//如果获取到操作标识，进行录入或者修改操作
if($action=="update")
{
    //获取表单传入数据
	$old_rmjj_id=$_POST["rmjj_id"];//热门剧集ID
	$rmjj_subject=$_POST["rmjj_subject"];//热门剧集标题
	$rmjj_options=$_POST["rmjj_options"];//热门剧集链接
	$rmjj_true=$_POST["rmjj_true"];//一句话描述热门剧集
    //传入数据过滤
    $old_rmjj_id=intval($old_rmjj_id);
    $rmjj_subject= string::un_script_code($rmjj_subject);
    $rmjj_options= string::un_script_code($rmjj_options);
    $rmjj_true= string::un_script_code($rmjj_true);
    //检测必填项
    
    if(!$rmjj_subject)
    {
		echo "<script>alert('请输入将要添加的热门剧集标题');history.back();</Script>";
		exit;
    
    }
    if(!$rmjj_options)
    {
		echo "<script>alert('请输入热门剧集链接');history.back();</Script>";
		exit;
    
    }
    if(!$rmjj_true)
    {
		echo "<script>alert('请输入将要添加的热门剧集的一句话描述');history.back();</Script>";
		exit;
    
    }
    //默认参数
    $nowtime=date("Y/m/d H:i:s",time());
    //如果是修改
    if($old_rmjj_id)
    {
        //修改
  		$sql = "update rmjj_tb set rmjj_subject='$rmjj_subject',rmjj_options='$rmjj_options',
        rmjj_true='$rmjj_true'
        where rmjj_id=$old_rmjj_id";
 		$mysql->runSql( $sql );
    }
    else
    {
        //新增
   		$sql = "insert into rmjj_tb (rmjj_subject,rmjj_options,rmjj_true,createtime,status) values ('$rmjj_subject',
        '$rmjj_options','$rmjj_true','$nowtime',1)";
 		$mysql->runSql( $sql );
   	
    }
    if( $mysql->errno() != 0 )
    {
        echo "<script>alert('".$mysql->errmsg() ."');history.back();</Script>";
        exit;
    }
    else
    {
        echo "<script>alert('操作成功！');location='rmjj_add.php?rmjj_id=$old_rmjj_id';</Script>";
        exit;    
    }
    
}    

$class_list=$mysql->getData("select class_name,class_id from class where status=1 order by class_fid asc");

?>
    <!--页面名称-->
	<h3>‘热门剧集’网页链接添加/修改<a href="rmjj_manager.php">返回>></a></h3>
    <!--表单开始-->
    <form action="?" method="post" name="class_add" id="class_add" enctype="multipart/form-data">
        <p>
            '热门剧集'标题：<input type="text" size=50 value="<?php echo $rmjj_value["rmjj_subject"];?>" name="rmjj_subject">
        </p>
        <p>
            '热门剧集'网页链接：<textarea name="rmjj_options" cols="40" rows="10"><?php echo $rmjj_value["rmjj_options"];?></textarea>
        </p>
        <p>
            '热门剧集'简述：<input type="text" size=50  value="<?php echo $rmjj_value["rmjj_true"];?>" name="rmjj_true">
        </p>
         <p>
             <!--隐藏参数，用来放置操作标示和修改的ID-->
            <input type="hidden" name="action"  value="update">
            <input type="hidden" name="rmjj_id" value="<?=$rmjj_value["rmjj_id"]?>">
             <!--表单提交-->
            <input type="submit" value="提交" />
        </p>
    </form>
</body>
</html>
