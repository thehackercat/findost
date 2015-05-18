<!DOCTYPE HTML>
<html>
<HEAD>
<meta charset="utf-8">
<TITLE>‘英语课堂’网页链接添加/修改</TITLE>
</HEAD>
<body>
    
<?php
include_once("base-class.php");

//新建sae数据库类
$mysql = new SaeMysql();

//获取英语课堂ID号传入
$yykt_id=intval($_GET["yykt_id"]);

//获取操作标识传入
$action=$_POST["action"];
$action= string::un_script_code($action);
$action= string::un_html($action);

//判断是否修改，如果传入了英语课堂ID，进行数据库查询获取全部内容
if($yykt_id)
{
	$yykt_value=$mysql->getLine("select * from yykt_tb where yykt_id=$yykt_id");
    if(!$yykt_value)
	{
		echo "<script>alert('无此<英语课堂>网页链接');history.back();</Script>";
		exit;
	}
}

//如果获取到操作标识，进行录入或者修改操作
if($action=="update")
{
    //获取表单传入数据
	$old_yykt_id=$_POST["yykt_id"];//英语课堂ID
	$yykt_subject=$_POST["yykt_subject"];//英语课堂标题
	$yykt_options=$_POST["yykt_options"];//英语课堂链接
	$yykt_true=$_POST["yykt_true"];//一句话描述英语课堂
    //传入数据过滤
    $old_yykt_id=intval($old_yykt_id);
    $yykt_subject= string::un_script_code($yykt_subject);
    $yykt_options= string::un_script_code($yykt_options);
    $yykt_true= string::un_script_code($yykt_true);
    //检测必填项
    
    if(!$yykt_subject)
    {
		echo "<script>alert('请输入将要添加的英语课堂标题');history.back();</Script>";
		exit;
    
    }
    if(!$yykt_options)
    {
		echo "<script>alert('请输入英语课堂链接');history.back();</Script>";
		exit;
    
    }
    if(!$yykt_true)
    {
		echo "<script>alert('请输入将要添加的英语课堂的一句话描述');history.back();</Script>";
		exit;
    
    }
    //默认参数
    $nowtime=date("Y/m/d H:i:s",time());
    //如果是修改
    if($old_yykt_id)
    {
        //修改
  		$sql = "update yykt_tb set yykt_subject='$yykt_subject',yykt_options='$yykt_options',
        yykt_true='$yykt_true'
        where yykt_id=$old_yykt_id";
 		$mysql->runSql( $sql );
    }
    else
    {
        //新增
   		$sql = "insert into yykt_tb (yykt_subject,yykt_options,yykt_true,createtime,status) values ('$yykt_subject',
        '$yykt_options','$yykt_true','$nowtime',1)";
 		$mysql->runSql( $sql );
   	
    }
    if( $mysql->errno() != 0 )
    {
        echo "<script>alert('".$mysql->errmsg() ."');history.back();</Script>";
        exit;
    }
    else
    {
        echo "<script>alert('操作成功！');location='yykt_add.php?yykt_id=$old_yykt_id';</Script>";
        exit;    
    }
    
}    

$class_list=$mysql->getData("select class_name,class_id from class where status=1 order by class_fid asc");

?>
    <!--页面名称-->
	<h3>‘英语课堂’网页链接添加/修改<a href="yykt_manager.php">返回>></a></h3>
    <!--表单开始-->
    <form action="?" method="post" name="class_add" id="class_add" enctype="multipart/form-data">
        <p>
            '英语课堂'标题：<input type="text" size=50 value="<?php echo $yykt_value["yykt_subject"];?>" name="yykt_subject">
        </p>
        <p>
            '英语课堂'网页链接：<textarea name="yykt_options" cols="40" rows="10"><?php echo $yykt_value["yykt_options"];?></textarea>
        </p>
        <p>
            '英语课堂'简述：<input type="text" size=50  value="<?php echo $yykt_value["yykt_true"];?>" name="yykt_true">
        </p>
         <p>
             <!--隐藏参数，用来放置操作标示和修改的ID-->
            <input type="hidden" name="action"  value="update">
            <input type="hidden" name="yykt_id" value="<?=$yykt_value["yykt_id"]?>">
             <!--表单提交-->
            <input type="submit" value="提交" />
        </p>
    </form>
</body>
</html>
