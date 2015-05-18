<!DOCTYPE HTML>
<html>
<HEAD>
<meta charset="utf-8">
<TITLE>‘今日导视’网页链接添加/修改</TITLE>
</HEAD>
<body>
    
<?php
include_once("base-class.php");

//新建sae数据库类
$mysql = new SaeMysql();

//获取今日导视外链ID号传入
$jrds_id=intval($_GET["jrds_id"]);

//获取操作标识传入
$action=$_POST["action"];
$action= string::un_script_code($action);
$action= string::un_html($action);

//判断是否修改，如果传入了今日导视ID，进行数据库查询获取全部内容
if($jrds_id)
{
	$jrds_value=$mysql->getLine("select * from jrds_tb where jrds_id=$jrds_id");
    if(!$jrds_value)
	{
		echo "<script>alert('无此<今日导视>网页链接');history.back();</Script>";
		exit;
	}
}

//如果获取到操作标识，进行录入或者修改操作
if($action=="update")
{
    //获取表单传入数据
	$old_jrds_id=$_POST["jrds_id"];//今日导视ID
	$jrds_subject=$_POST["jrds_subject"];//今日导视标题
	$jrds_options=$_POST["jrds_options"];//今日导视链接
	$jrds_true=$_POST["jrds_true"];//一句话描述今日导视
    //传入数据过滤
    $old_jrds_id=intval($old_jrds_id);
    $jrds_subject= string::un_script_code($jrds_subject);
    $jrds_options= string::un_script_code($jrds_options);
    $jrds_true= string::un_script_code($jrds_true);
    //检测必填项
    
    if(!$jrds_subject)
    {
		echo "<script>alert('请输入将要添加的今日导视标题');history.back();</Script>";
		exit;
    
    }
    if(!$jrds_options)
    {
		echo "<script>alert('请输入今日导视链接');history.back();</Script>";
		exit;
    
    }
    if(!$jrds_true)
    {
		echo "<script>alert('请输入将要添加的今日导视的一句话描述');history.back();</Script>";
		exit;
    
    }
    //默认参数
    $nowtime=date("Y/m/d H:i:s",time());
    //如果是修改
    if($old_jrds_id)
    {
        //修改
  		$sql = "update jrds_tb set jrds_subject='$jrds_subject',jrds_options='$jrds_options',
        jrds_true='$jrds_true'
        where jrds_id=$old_jrds_id";
 		$mysql->runSql( $sql );
    }
    else
    {
        //新增
   		$sql = "insert into jrds_tb (jrds_subject,jrds_options,jrds_true,createtime,status) values ('$jrds_subject',
        '$jrds_options','$jrds_true','$nowtime',1)";
 		$mysql->runSql( $sql );
   	
    }
    if( $mysql->errno() != 0 )
    {
        echo "<script>alert('".$mysql->errmsg() ."');history.back();</Script>";
        exit;
    }
    else
    {
        echo "<script>alert('操作成功！');location='jrds_add.php?jrds_id=$old_jrds_id';</Script>";
        exit;    
    }
    
}    

$class_list=$mysql->getData("select class_name,class_id from class where status=1 order by class_fid asc");

?>
    <!--页面名称-->
	<h3>‘今日导视’网页链接添加/修改<a href="jrds_manager.php">返回>></a></h3>
    <!--表单开始-->
    <form action="?" method="post" name="class_add" id="class_add" enctype="multipart/form-data">
        <p>
            '今日导视'标题：<input type="text" size=50 value="<?php echo $jrds_value["jrds_subject"];?>" name="jrds_subject">
        </p>
        <p>
            '今日导视'网页链接：<textarea name="jrds_options" cols="40" rows="10"><?php echo $jrds_value["jrds_options"];?></textarea>
        </p>
        <p>
            '今日导视'简述：<input type="text" size=50  value="<?php echo $jrds_value["jrds_true"];?>" name="jrds_true">
        </p>
         <p>
             <!--隐藏参数，用来放置操作标示和修改的ID-->
            <input type="hidden" name="action"  value="update">
            <input type="hidden" name="jrds_id" value="<?=$jrds_value["jrds_id"]?>">
             <!--表单提交-->
            <input type="submit" value="提交" />
        </p>
    </form>
</body>
</html>
