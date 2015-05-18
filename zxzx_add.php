<!DOCTYPE HTML>
<html>
<HEAD>
<meta charset="utf-8">
<TITLE>‘最新资讯’网页链接添加/修改</TITLE>
</HEAD>
<body>
    
<?php
include_once("base-class.php");

//新建sae数据库类
$mysql = new SaeMysql();

//获取最新资讯外链ID号传入
$zxzx_id=intval($_GET["zxzx_id"]);

//获取操作标识传入
$action=$_POST["action"];
$action= string::un_script_code($action);
$action= string::un_html($action);

//判断是否修改，如果传入了最新资讯外链ID，进行数据库查询获取全部内容
if($zxzx_id)
{
    $zxzx_value=$mysql->getLine("select * from zxzx_tb where zxzx_id=$zxzx_id");
    if(!$zxzx_value)
    {
        echo "<script>alert('无此<最新资讯>网页链接');history.back();</Script>";
        exit;
    }
}

//如果获取到操作标识，进行录入或者修改操作
if($action=="update")
{
    //获取表单传入数据
    $old_zxzx_id=$_POST["zxzx_id"];//最新资讯ID
    $zxzx_subject=$_POST["zxzx_subject"];//最新资讯标题
    $zxzx_options=$_POST["zxzx_options"];//最新资讯链接
    $zxzx_true=$_POST["zxzx_true"];//一句话描述最新资讯
    //传入数据过滤
    $old_zxzx_id=intval($old_zxzx_id);
    $zxzx_subject= string::un_script_code($zxzx_subject);
    $zxzx_options= string::un_script_code($zxzx_options);
    $zxzx_true= string::un_script_code($zxzx_true);
    //检测必填项
    
    if(!$zxzx_subject)
    {
        echo "<script>alert('请输入将要添加的最新资讯标题');history.back();</Script>";
        exit;
    
    }
    if(!$zxzx_options)
    {
        echo "<script>alert('请输入最新资讯链接');history.back();</Script>";
        exit;
    
    }
    if(!$zxzx_true)
    {
        echo "<script>alert('请输入将要添加的最新资讯的一句话描述');history.back();</Script>";
        exit;
    
    }
    //默认参数
    $nowtime=date("Y/m/d H:i:s",time());
    //如果是修改
    if($old_zxzx_id)
    {
        //修改
        $sql = "update zxzx_tb set zxzx_subject='$zxzx_subject',zxzx_options='$zxzx_options',
        zxzx_true='$zxzx_true'
        where zxzx_id=$old_zxzx_id";
        $mysql->runSql( $sql );
    }
    else
    {
        //新增
        $sql = "insert into zxzx_tb (zxzx_subject,zxzx_options,zxzx_true,createtime,status) values ('$zxzx_subject',
        '$zxzx_options','$zxzx_true','$nowtime',1)";
        $mysql->runSql( $sql );
    
    }
    if( $mysql->errno() != 0 )
    {
        echo "<script>alert('".$mysql->errmsg() ."');history.back();</Script>";
        exit;
    }
    else
    {
        echo "<script>alert('操作成功！');location='zxzx_add.php?zxzx_id=$old_zxzx_id';</Script>";
        exit;    
    }
    
}    

$class_list=$mysql->getData("select class_name,class_id from class where status=1 order by class_fid asc");

?>
    <!--页面名称-->
    <h3>‘最新资讯’网页链接添加/修改<a href="zxzx_manager.php">返回>></a></h3>
    <!--表单开始-->
    <form action="?" method="post" name="class_add" id="class_add" enctype="multipart/form-data">
        <p>
            '最新资讯'标题：<input type="text" size=50 value="<?php echo $zxzx_value["zxzx_subject"];?>" name="zxzx_subject">
        </p>
        <p>
            '最新资讯'网页链接：<textarea name="zxzx_options" cols="40" rows="10"><?php echo $zxzx_value["zxzx_options"];?></textarea>
        </p>
        <p>
            '最新资讯'简述：<input type="text" size=50  value="<?php echo $zxzx_value["zxzx_true"];?>" name="zxzx_true">
        </p>
         <p>
             <!--隐藏参数，用来放置操作标示和修改的ID-->
            <input type="hidden" name="action"  value="update">
            <input type="hidden" name="zxzx_id" value="<?=$zxzx_value["zxzx_id"]?>">
             <!--表单提交-->
            <input type="submit" value="提交" />
        </p>
    </form>
</body>
</html>
