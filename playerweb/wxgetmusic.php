<?php
include_once("base-class.php");

//定义三个数组
$g_cgival = array();
$g_pro = array();
$g_show = array();

//获取传递参数函数
function check_cgi_pro()
{
    global $g_cgival, $g_pro, $g_show;
    //获取url传递过来的参数
    cgi::both($g_cgival['callback'],"callback","");
    cgi::both($g_cgival["t"], "t", "");
    cgi::both($g_cgival['do'],"do","");
    cgi::both($g_cgival['open_id'],"open_id","");
    cgi::both($g_cgival["song_id"], "song_id", 0);
    //如果page为空则默认为1
    cgi::both($g_cgival["page"], "page", 1);
    
    //防注入过滤
    $g_cgival['t']= string::un_script_code($g_cgival['t']);
    $g_cgival['t']= string::un_html($g_cgival['t']);
    $g_cgival['callback']= string::un_script_code($g_cgival['callback']);
    $g_cgival['callback']= string::un_html($g_cgival['callback']);
    $g_cgival['do']= string::un_script_code($g_cgival['do']);
    $g_cgival['do']= string::un_html($g_cgival['do']);
    $g_cgival['open_id']= string::un_script_code($g_cgival['open_id']);
    $g_cgival['open_id']= string::un_html($g_cgival['open_id']);
    $g_cgival["song_id"]=intval($g_cgival["song_id"]);
    $g_cgival["page"]=intval($g_cgival["page"]);
}

function get_data()
{
    global $g_cgival, $g_pro, $g_show;
    
    //新建mysql类
    $mysql = new SaeMysql();
    
    
    //所有歌曲列表
    if($g_cgival['do']=="list")
    {
            //计算总数
            $count=$mysql->getVar("select COUNT(*)  from music where status=1");
            if($count)
            {
                //定义歌曲列表数组
                $music_list=array();
                //每页显示5首歌曲
                $page_num=5;
                //当前页码
                $page=$g_cgival["page"];
                //起始歌曲记录号
                $from_record = ($page - 1) * $page_num;
                //获取歌曲列表
                $music_list=$mysql->getData("select * from music where  status=1  order by mid desc limit $from_record,$page_num");
                //检测是否还有下页
                $music_next=0;
                $real_page=@ceil($count / $page_num);
                if($real_page>1)
                {
                    if($page>=$real_page)
                    {
                        $music_next=0;
                    }
                    else
                    {
                        $music_next=$page+1;
                    }
                }
                $music_prev=$page-1;
                
                //返回数组赋值
                $g_show["music_list"]=$music_list;
                $g_show["music_next"]=$music_next;
                $g_show["music_prev"]=$music_prev;
                $g_show["page"]=$page;
                $g_show["real_page"]=$real_page;
               
            }
            else
            {
                throw new Exception("歌曲列表获取出错，请返回对话框后重新进入");
             }
    }
    //喜欢歌曲列表
    elseif($g_cgival['do']=="like_list")
    {
        //判断是否获取到用户Openid
        if($g_cgival['open_id'])
        {
            $count=$mysql->getVar("select COUNT(*)  from music_user where openid='".$g_cgival['open_id']."' and status=1 ");
            if($count)
            {
                //定义歌曲列表数组
                $music_list=array();
                //每页显示5首歌曲
                $page_num=5;
                //当前页码
                $page=$g_cgival["page"];
                //起始歌曲记录号
                $from_record = ($page - 1) * $page_num;
                //获取歌曲列表
                $music_list=$mysql->getData("select * from music_user where openid='".$g_cgival['open_id']."' and status=1  order by dateline desc limit $from_record,$page_num");
                //检测是否还有下页
                $music_next=0;
                $real_page=@ceil($count / $page_num);
                if($real_page>1)
                {
                    if($page>=$real_page)
                    {
                        $music_next=0;
                    }
                    else
                    {
                        $music_next=$page+1;
                    }
                }
                $music_prev=$page-1;
                
                //返回数组赋值
                $g_show["music_list"]=$music_list;
                $g_show["music_next"]=$music_next;
                $g_show["music_prev"]=$music_prev;
                $g_show["page"]=$page;
                $g_show["real_page"]=$real_page;
               
            }
            else
            {
                //没有获取到列表错误提示
                throw new Exception("你还没有喜欢的歌曲列表，请在播放器里点击爱心选择你钟爱的歌曲先");
             }
            
        }
        else
        {
            //没有获取到openid的提示
            throw new Exception("请先在微信搜索“whenicyouagain”关注「美剧插曲妞」的公众账号\n");
        }
    
    }
    //用户点击喜欢歌曲
    elseif($g_cgival['do']=="like")
    {
        //监测是否获取openid和歌曲id
       if($g_cgival['open_id'] && $g_cgival["song_id"])
       {
           //获取用户是否已经对这首歌点击过喜欢
            $like_song=$mysql->getLine("select *  from music_user where openid='".$g_cgival['open_id']."' and mid=".$g_cgival["song_id"]);
           //如果已经点击过
           if($like_song)
           {
               //判断当前是喜欢还是不喜欢，如果是喜欢则变为不喜欢
               $like_flag=($like_song["status"]==1)?0:1;
               //更新喜欢状态
               $mysql->runSql( "update music_user set status=$like_flag where openid='".$g_cgival['open_id']."' and mid=".$g_cgival["song_id"] );
           
           }
           else
           {
               //没有表态过则先获取歌曲信息
                $current_song=$mysql->getLine("select * from music where status=1 and mid=".$g_cgival["song_id"]." limit 0,1");
               
               //新增一条表态记录
                $mysql->runSql("insert into music_user (openid,dateline,mid,title,cover,singer,intro,status) VALUES 
                ('".$g_cgival['open_id']."',".time().",".$current_song["mid"].",'".mysql_escape_string($current_song["title"])."','".$current_song["cover"]."','".mysql_escape_string($current_song["singer"])."','".mysql_escape_string($current_song["intro"])."',1)");
               
               //设定表态标识为喜欢
               $like_flag=1;
           }
            //返回数组赋值
           $g_show["like_flag"]=$like_flag;
       }
       else
       {
        //没有获取到openid的提示
          throw new Exception("请先在微信搜索“whenicyouagain”关注「美剧插曲妞」的公众账号\n");
      
       }
        
    }
    else
    {
        
        //播放界面
        
        
        //如果有歌曲ID则获取歌曲ID
        if($g_cgival["song_id"]>0)
        {
            $current_song=$mysql->getLine("select * from music where  status=1  and mid=".$g_cgival["song_id"]." limit 0,1");
        }
        //如果歌曲播放到最后则循环
        elseif($g_cgival["song_id"]=="-1")
        {
             $current_song=$mysql->getLine("select * from music where status=1  order by mid asc limit 0,1");
       
        }
        else//如果没有歌曲ID则获取最新歌曲
        {
            $current_song=$mysql->getLine("select * from music where  status=1 order by mid desc limit 0,1");
         
        }
        //获取到当前播放歌曲之后获取上一首和下一首
        if($current_song)
        {
            //获取前后歌曲
            $prev_id=$mysql->getVar("select mid from music where status=1 and mid<$current_song[mid] order by mid desc limit 0,1");  
            $next_id=$mysql->getVar("select mid from music where status=1 and mid>$current_song[mid] order by mid asc limit 0,1");  
            if(!$prev_id)$prev_id=0;
            if(!$next_id)$next_id=0;
        }
        else
        {
            throw new Exception("歌曲获取出错，请返回对话框后重新进入");
        }
        //检查歌曲是否like
        $like_song=0;
        if($g_cgival['open_id'])
        {
            $like_song=$mysql->getVar("select COUNT(*)  from music_user where openid='".$g_cgival['open_id']."' and status=1 and mid=$current_song[mid]");
        }
        //返回数组赋值
        $g_show["songtitle"]=$current_song["title"];
        $g_show["auther"]=$current_song["singer"];
        $g_show["cover"]=$current_song["cover"];
        $g_show["songintro"]=$current_song["intro"];
        $g_show["url"]=$current_song["url"];
        $g_show["prev_id"]= $prev_id;
        $g_show["next_id"]= $next_id;
        $g_show["current_id"]= $current_song["mid"];
        $g_show["like_song"]=intval($like_song);
    }
} 

try
{
    check_cgi_pro();
    get_data();
    $g_show["error"] = "0";
    $g_show["errmsg"] = ""; 
}
catch(Exception $e)
{
    $g_show["error"] = "1";
    $g_show["errmsg"] = $e->getMessage();
}
show_pro();
exit;

//输出数组
function show_pro()
{
    global $g_cgival, $g_pro, $g_show,$config;
    if ($g_cgival['t'] == "serialize")
    {
        echo serialize($g_show);
    }
    elseif ($g_cgival['t'] == "json")
    {
        echo json_encode($g_show);
    }
    elseif($g_cgival['t'] == "jsonp")
    {
        echo $g_cgival['callback'] ."(".json_encode($g_show).")";
    }
    else
    {
        echo serialize($g_show);
    }
    
    
    unset($g_cgival);
    unset($g_pro);
    unset($g_show);
    unset($config);
}
