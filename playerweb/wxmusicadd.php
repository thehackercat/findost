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

//定义歌曲数组
$song_value=array();

//获取url传递过来的歌曲id
$id=intval($_GET["id"]);

//获取表单传递的操作标记
$action=$_POST["action"];
$action= string::un_script_code($action);
$action= string::un_html($action);

//如果有歌曲ID表示修改歌曲信息，先获取该歌曲的信息
if($id)
{
		$song_value = $mysql->getLine("select * from music where mid=$id and status=1");
    	if(!$song_value)
        {
            echo "<script>alert('没有这首歌');history.back();</Script>";
            exit;
        }
               
}

//执行修改或者新增操作
if($action=="update")
{
    
    //获取表单数据
	$music_id=trim($_POST["id"]);
	$old_cover=trim($_POST["old_cover"]);
	$intro=trim($_POST["intro"]);
	$title=trim($_POST["title"]);
	$singer=trim($_POST["singer"]);
	$url=trim($_POST["url"]);
    
    //防注入
	$old_cover= mysql_escape_string(string::un_script_code($old_cover));
	$intro= mysql_escape_string(string::un_script_code($intro));
	$title= mysql_escape_string(string::un_script_code($title));
	$singer= mysql_escape_string(string::un_script_code($singer));
	$url= mysql_escape_string(string::un_script_code($url));
	$music_id= intval($music_id);
    
    //验证表单是否填写完整
	if(!$intro || !$title || !$singer || !$url)
	{
		echo "<script>alert('表单输入不完整');history.back();</Script>";
		exit;
	}
    //验证是否有封面图片
	if($_FILES['cover']['name']=='' && $old_cover=='')
	{
		echo "<script>alert('请上传图片!');history.back();</Script>";
		exit;
	}
    //如果有图片上传
	if($_FILES['cover']['name']!='')
	{
        //允许上传文件类型
        $file_ext_arr=array("jpg","png","jpeg");
        
        //检测上传文件后缀    
        $get_exts = explode('.',$_FILES['cover']['name']);
        $exts_len=count($get_exts);
        $file_ext=strtolower($get_exts[$exts_len-1]);
        
        if(!in_array($file_ext,$file_ext_arr))
        {
            echo "<script>alert('只允许上传后缀为jpg、png、jpeg的图片文件!');history.back();</Script>";
            exit;
        }
        //设定新文件名称
        $new_filename=substr(md5($_FILES['cover']['name']),0,6).date("YmdHis").".".$file_ext;
        
        //新建Storage类
         $s = new SaeStorage();
        //上传到SAE的Storage里，注意修改存储空间的名字“weixincourse”，并把返回的文件名赋值给旧文件变量old_roster_pic
        $old_cover=$s->upload( 'findost' , $new_filename , $_FILES['cover']['tmp_name'] ); 
        
	}
    //如果是修改歌曲
	if($music_id)
	{
        
        $mysql->runSql("update music set title='".$title."',singer='".$singer."',
        cover='".$old_cover."',intro='".$intro."',url='".$url."' where mid=$music_id");
	}
    //新增歌曲
	else
	{
        $mysql->runSql("insert into music (title,singer,cover,intro,url) VALUES 
                ('".$title."','".$singer."','".$old_cover."','".$intro."','".$url."')");
	}
	echo "<script>alert('更新成功！');location='wxmusicadd.php?id=$music_id';</Script>";
	exit;
}
?>

					<div class="home_promo_container" style="padding-bottom:10px;">
						<h3 style="font-size:14px;">
							音乐添加/修改&nbsp;&nbsp;<a href="wxmusic.php">返回>></a>
							</h3>
					<div id="respond2" class="order_search">
						<form action="?" method="post" name="check_order" id="check_order" enctype="multipart/form-data">
							<p>
								<span style="font-weight:bold;font-size:14px;">　<span class="required2">*</span>封面：</span>
								<input type="file" value="" name="cover" id="cover">图片大小300*300
								<input type="hidden" value="<?=$song_value["cover"]?>" name="old_cover" id="old_cover">
								<?if($song_value["cover"]) {?><br><img src="<?=$song_value["cover"]?>"  width="100"><?}?>
							</p>
							<p>
								<span style="font-weight:bold;font-size:14px;">　<span class="required2">*</span>标题：</span>
								<input type="text" value="<?=$song_value["title"]?>" name="title" id="title" size=60>
							</p>
							<p>
								<span style="font-weight:bold;font-size:14px;">　<span class="required2">*</span>歌手：</span>
								<input type="text" value="<?=$song_value["singer"]?>" name="singer" id="singer" size=60>
							</p>
							<p>
								<span style="font-weight:bold;font-size:14px;">　<span class="required2">*</span>链接：</span>
								<input type="text" value="<?=$song_value["url"]?>" name="url" id="url" size=60>
							</p>
							<p>
								<span style="font-weight:bold;font-size:14px;">　<span class="required2">*</span>解说：</span>
								<input type="text" value="<?=$song_value["intro"]?>" name="intro" id="intro" size=60>
							</p>
							<p class="form-submit">
								<input type="hidden" name="action" id="action" value="update">
								<input type="hidden" name="id" id="id" value="<?=$id?>">
								<input name="send_msg" type="button" onClick="check_submit();" id="send_msg" value="提交" />
							</p>
							<div class="checkTips"></div>
						</form>
					</div><!-- #respond -->
						<div class="clear_both"></div>
				</div>
                <div class="clear_both"></div>
            </div>
        </div>
	</body>

</html>
<script type="text/javascript">
<!--
    
	//空字符值; 
	function isEmpty(s){ 
		s = trim(s); 
		return s.length == 0; 
	} 
	//空字符值; 
	function strLength(s){ 
		s = trim(s); 
		return s.length; 
	} 
	//去左右空格; 
	function trim(s){ 
		return rtrim(ltrim(s)); 
	} 
	//去左空格; 
	function ltrim(s){ 
		return s.replace( /^\s*/, ""); 
	} 
	//去右空格; 
	function rtrim(s){ 
		return s.replace( /\s*$/, ""); 
	}
    //js验证表单
	var send_form=0;
	function check_submit()
	{
		$(".checkTips").html("");
		if(send_form==1) return false;
		send_form=1;
		if(isEmpty($("#old_cover").val()) && isEmpty($("#cover").val()) )
		{
			$(".checkTips").html("请上传唱片封面");
			send_form=0;
			return false;
		}
		if(isEmpty($("#title").val()))
		{
			$(".checkTips").html("请输入标题");
			send_form=0;
			return false;
		}
		if(isEmpty($("#singer").val()))
		{
			$(".checkTips").html("请输入歌手");
			send_form=0;
			return false;
		}
		if(isEmpty($("#url").val()))
		{
			$(".checkTips").html("请输入链接");
			send_form=0;
			return false;
		}
		if(isEmpty($("#intro").val()))
		{
			$(".checkTips").html("请输入说明");
			send_form=0;
			return false;
		}
		if(send_form==1)
		{
			$("#check_order").submit();
		}
	}


</script>
