<!DOCTYPE HTML>
<html>
<HEAD>
<meta charset="utf-8">
<TITLE>‘经典再现’网页链接添加/修改</TITLE>
</HEAD>
<body>
    
<?php
include_once("base-class.php");

//新建sae数据库类
$mysql = new SaeMysql();

//获取经典再现外链ID号传入
$jdzx_id=intval($_GET["jdzx_id"]);

//获取操作标识传入
$action=$_POST["action"];
$action= string::un_script_code($action);
$action= string::un_html($action);

//判断是否修改，如果传入了经典再现ID，进行数据库查询获取全部内容
if($jdzx_id)
{
	$jdzx_value=$mysql->getLine("select * from jdzx_tb where jdzx_id=$jdzx_id");
    if(!$jdzx_value)
	{
		echo "<script>alert('无此<经典再现>网页链接');history.back();</Script>";
		exit;
	}
}

//如果获取到操作标识，进行录入或者修改操作
if($action=="update")
{
    //获取表单传入数据
	$old_jdzx_id=$_POST["jdzx_id"];//经典再现ID
	$jdzx_subject=$_POST["jdzx_subject"];//经典再现标题
	$jdzx_options=$_POST["jdzx_options"];//经典再现链接
	$jdzx_true=$_POST["jdzx_true"];//一句话描述经典再现
    //传入数据过滤
    $old_jdzx_id=intval($old_jdzx_id);
    $jdzx_subject= string::un_script_code($jdzx_subject);
    $jdzx_options= string::un_script_code($jdzx_options);
    $jdzx_true= string::un_script_code($jdzx_true);
    //检测必填项
    
    if(!$jdzx_subject)
    {
		echo "<script>alert('请输入将要添加的经典再现标题');history.back();</Script>";
		exit;
    
    }
    if(!$jdzx_options)
    {
		echo "<script>alert('请输入经典再现链接');history.back();</Script>";
		exit;
    
    }
    if(!$jdzx_true)
    {
		echo "<script>alert('请输入将要添加的经典再现的一句话描述');history.back();</Script>";
		exit;
    
    }
    //默认参数
    $nowtime=date("Y/m/d H:i:s",time());
    //如果是修改
    if($old_jdzx_id)
    {
        //修改
  		$sql = "update jdzx_tb set jdzx_subject='$jdzx_subject',jdzx_options='$jdzx_options',
        jdzx_true='$jdzx_true'
        where jdzx_id=$old_jdzx_id";
 		$mysql->runSql( $sql );
    }
    else
    {
        //新增
   		$sql = "insert into jdzx_tb (jdzx_subject,jdzx_options,jdzx_true,createtime,status) values ('$jdzx_subject',
        '$jdzx_options','$jdzx_true','$nowtime',1)";
 		$mysql->runSql( $sql );
   	
    }
    if( $mysql->errno() != 0 )
    {
        echo "<script>alert('".$mysql->errmsg() ."');history.back();</Script>";
        exit;
    }
    else
    {
        echo "<script>alert('操作成功！');location='jdzx_add.php?jdzx_id=$old_jdzx_id';</Script>";
        exit;    
    }
    
}    

$class_list=$mysql->getData("select class_name,class_id from class where status=1 order by class_fid asc");

?>
    <!--页面名称-->
	<h3>‘经典再现’网页链接添加/修改<a href="jdzx_manager.php">返回>></a></h3>
    <!--表单开始-->
    <form action="?" method="post" name="class_add" id="class_add" enctype="multipart/form-data">
        <p>
            '经典再现'标题：<input type="text" size=50 value="<?php echo $jdzx_value["jdzx_subject"];?>" name="jdzx_subject">
        </p>
        <p>
            '经典再现'网页链接：<textarea name="jdzx_options" cols="40" rows="10"><?php echo $jdzx_value["jdzx_options"];?></textarea>
        </p>
        <p>
            '经典再现'简述：<input type="text" size=50  value="<?php echo $jdzx_value["jdzx_true"];?>" name="jdzx_true">
        </p>
         <p>
             <!--隐藏参数，用来放置操作标示和修改的ID-->
            <input type="hidden" name="action"  value="update">
            <input type="hidden" name="jdzx_id" value="<?=$jdzx_value["jdzx_id"]?>">
             <!--表单提交-->
            <input type="submit" value="提交" />
        </p>
    </form>
</body>
</html>
