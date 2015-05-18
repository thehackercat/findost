<!DOCTYPE HTML>
<html>
<HEAD>
<meta charset="utf-8">
<TITLE>影视原声下载地址添加/修改</TITLE>
</HEAD>
<body>
    
<?php
include_once("base-class.php");

//新建sae数据库类
$mysql = new SaeMysql();

//获取影视ID号传入
$question_id=intval($_GET["question_id"]);

//获取操作标识传入
$action=$_POST["action"];
$action= string::un_script_code($action);
$action= string::un_html($action);

//判断是否修改，如果传入了影视原声ID，进行数据库查询获取全部内容
if($question_id)
{
	$question_value=$mysql->getLine("select * from question_tb where question_id=$question_id");
    if(!$question_value)
	{
		echo "<script>alert('无此原声下载链接');history.back();</Script>";
		exit;
	}
}

//如果获取到操作标识，进行录入或者修改操作
if($action=="update")
{
    //获取表单传入数据
	$old_question_id=$_POST["question_id"];
	$question_subject=$_POST["question_subject"];
	$question_options=$_POST["question_options"];
	$question_true=$_POST["question_true"];
    //传入数据过滤
    $old_question_id=intval($old_question_id);
    $question_subject= string::un_script_code($question_subject);
    $question_options= string::un_script_code($question_options);
    $question_true= string::un_script_code($question_true);
    //检测必填项
    
    if(!$question_subject)
    {
		echo "<script>alert('请输入将要添加的影视名');history.back();</Script>";
		exit;
    
    }
    if(!$question_options)
    {
		echo "<script>alert('请输入原声曲名');history.back();</Script>";
		exit;
    
    }
    if(!$question_true)
    {
		echo "<script>alert('请输入将要添加的影视原声的下载地址');history.back();</Script>";
		exit;
    
    }
    //默认参数
    $nowtime=date("Y/m/d H:i:s",time());
    //如果是修改
    if($old_question_id)
    {
        //修改
  		$sql = "update question_tb set question_subject='$question_subject',question_options='$question_options',
        question_true='$question_true'
        where question_id=$old_question_id";
 		$mysql->runSql( $sql );
    }
    else
    {
        //新增
   		$sql = "insert into question_tb (question_subject,question_options,question_true,createtime,status) values ('$question_subject',
        '$question_options','$question_true','$nowtime',1)";
 		$mysql->runSql( $sql );
   	
    }
    if( $mysql->errno() != 0 )
    {
        echo "<script>alert('".$mysql->errmsg() ."');history.back();</Script>";
        exit;
    }
    else
    {
        echo "<script>alert('操作成功！');location='question_add.php?question_id=$old_question_id';</Script>";
        exit;    
    }
    
}    

$class_list=$mysql->getData("select class_name,class_id from class where status=1 order by class_fid asc");

?>
    <!--页面名称-->
	<h3>影视原声下载地址添加/修改<a href="question_manager.php">返回>></a></h3>
    <!--表单开始-->
    <form action="?" method="post" name="class_add" id="class_add" enctype="multipart/form-data">
        <p>
            影视名（美剧第几季或者电影名）：<input type="text" size=50 value="<?php echo $question_value["question_subject"];?>" name="question_subject">
        </p>
        <p>
            原声曲名：<textarea name="question_options" cols="40" rows="10"><?php echo $question_value["question_options"];?></textarea>
        </p>
        <p>
            下载地址：<input type="text" size=50  value="<?php echo $question_value["question_true"];?>" name="question_true">
        </p>
         <p>
             <!--隐藏参数，用来放置操作标示和修改的ID-->
            <input type="hidden" name="action"  value="update">
            <input type="hidden" name="question_id" value="<?=$question_value["question_id"]?>">
             <!--表单提交-->
            <input type="submit" value="提交" />
        </p>
    </form>
</body>
</html>
