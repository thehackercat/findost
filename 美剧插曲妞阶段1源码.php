<?php
/*
surprise in wx_tpl.
*/




//装载模板文件
include_once("wx_tpl.php");
include_once("base-class.php");
include_once("wxfindost.php");


//新建sae数据库类
$mysql = new SaeMysql();

//新建Memcache类
$mc=memcache_init();

//获取微信发送数据
$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

//操作菜单
$help_menu="";

//新建baiduMusicAPI类
$apiFunc = new apiFunction();

//返回回复数据
if (!empty($postStr))
{
          
        //解析数据
          $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        //发送消息方ID
          $fromUsername = $postObj->FromUserName;
        //接收消息方ID
          $toUsername = $postObj->ToUserName;
        //消息类型
          $form_MsgType = $postObj->MsgType;
    
        //文字消息
        if($form_MsgType=="text") //判定用户输入进来的是否是文本信息
        {
            //$form_Content = trim($postObj->Content);//删除传入文本信息中的空格
            $form_Content = $postObj->Content;
            $form_Content = string::un_script_code($form_Content);//过滤文本信息
        
            if(!empty($form_Content))
            {
                $str_music = mb_substr($form_Content, 0, 1, "UTF-8");
                $str_explode = mb_substr($form_Content, 1, 30, "UTF-8");

                $req_music = explode('#|＃', $str_explode);
                //$req_ost = explode('@|＠',$str_explode);
                //$song = mb_substr($keyword, 1, 220, "UTF-8");
                $song = $req_music[0];
                $singer = $req_music[1];

                if ($str_music != '#' && $str_music != '＃'&& $str_music != '@' && $str_music != '＠')
                {
                    $contentStr = "输入格式不正确哦\n".
                    "原声点播请输入：#+歌名 或者 #+歌名+#+歌手\n\n"."影视原声查询请输入: @+影视名";
                    $msgType = "text";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $contentStr);
                    echo $resultStr;
                    exit;
                }
                elseif ($str_movie ='@' || $str_movie ='＠') 
                {
                    //获取影视名
                    $moviename = $str_explode;
                    //获取符合条件的数据
                    $ost_list=$mysql->getData("select *
                                            from question_tb  
                                            where question_subject = '$moviename'
                                            order by question_id desc 
                                            limit 0,50");
                    if(!empty($ost_list))
                    {
                        foreach($ost_list as $value)
                        {
                            //原声曲名
                            $ost_content = $value[question_options];
                            //原声下载地址
                            $ost_dl_list = $value[question_true];
                        }

                        $contentStr = "您所查询的<".$moviename.">相关原声如下: \n".$ost_content."\n\n下载戳它".
                        "$ost_dl_list";

                        $msgType = "text";
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $contentStr);
                        echo $resultStr;
                        exit; 
                    }else
                    {
                        if($str_movie ='#' || $str_movie ='＃')
                        {
                            //功能二：返回点播音频
                            $url_arr = $apiFunc->baiduMusic($song, $singer);
                            if (empty($url_arr))
                            {
                                $contentStr = "非常抱歉哦,T.T".
                                "暂时没有相关资源，可以换一个嘛~~[微笑]";
                                $msgType = "text";
                                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $contentStr);
                                echo $resultStr;
                                exit;  
                            }
                            else
                            {
                                $msgType = "music";
                                //$resultStr = sprintf($musicTpl, $object->FromUserName,$object->ToUserName,$song,$singer,$url_arr['url'],$url_arr['durl']);
                                $resultStr = sprintf($musicTpl, $fromUsername,$toUsername, $time, $msgType,$song,$singer,$url_arr['url'],$url_arr['durl']);
                                echo $resultStr;
                                exit;
                            }
                        }
                        $contentStr = "非常抱歉哦,T.T".
                            "暂时没有相关影视原声资源，可以换一个嘛~~[微笑]";
                            $msgType = "text";
                            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $contentStr);
                            echo $resultStr;
                            exit;  
                    }
                }else
                {
                        //功能二：返回点播音频
                        $url_arr = $apiFunc->baiduMusic($song, $singer);
                        if (empty($url_arr))
                        {
                            $contentStr = "非常抱歉哦,T.T".
                            "暂时没有相关资源，可以换一个嘛~~[微笑]";
                            $msgType = "text";
                            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $contentStr);
                            echo $resultStr;
                            exit;  
                        }
                        else
                        {
                            $msgType = "music";
                            //$resultStr = sprintf($musicTpl, $object->FromUserName,$object->ToUserName,$song,$singer,$url_arr['url'],$url_arr['durl']);
                            $resultStr = sprintf($musicTpl, $fromUsername,$toUsername, $time, $msgType,$song,$singer,$url_arr['url'],$url_arr['durl']);
                            echo $resultStr;
                            exit;
                        }
                }

            }
            else
            { 
                $msgType = "text";
                $resultStr = sprintf($textTpl,$fromUsername,$toUsername,$time,$msgType,"不要不理我T.T...");//可以看这个例子=3=
                echo $resultStr;
                exit;
            }
        }
    
        //事件消息
         if($form_MsgType=="event")
        {
            //获取事件类型
            $form_Event = $postObj->Event;
              
              
            //关注语  
            if($form_Event=="subscribe")
            {

                //第一阶段关注语，等待修改中。。。
                $welcome_str="你好我是「美剧插曲妞」开发测试号，欢迎关注我~~~ \n";
                
                //指定消息类型（查看wx_tpl.php）
                $msgType = "text";
                
                //$resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $welcome_str); <-----$welcome_str可改成任意字段
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $welcome_str);
                echo $resultStr;
                exit;  
            }
        
            //获取自定义菜单点击事件
              
            if($form_Event=="CLICK")
            {
               
                //获取菜单key值
                $form_EventKey = trim($postObj->EventKey);
                
                //影视原声-原声OST-文字类消息
                if($form_EventKey=="ysys") //与make_menu.php里的key值对应
                {

                    /*
                    解释一下该图文模板：
                    <ToUserName> 这里填入每个微信公众账号传回来的fromUsername的值</ToUserName>
                    <FromUserName> 这里是我根据模板获取的每个向该公众账号发送消息的用户的UserName  </FromeUserName>
                    <CreateTime>".time()"获取当前时间 </CreateTime>
                    <MsgType>图文为news 文字为text </MsgType>
                    <ArticleCount>下拉列表的个数 </ArticleCount>
                    
                    <Title>标签名</Title>
                    <Description> 对该标签的描述，一般不填写.</Description>
                    <PicUrl>图文样式里 图片的url</PicUrl>
                    <Url></Url>
                    
                    <Funcflag>标记星标的字段个数 </Funcflag>
                    
                    在该网站有详细解释 http://www.jb51.net/article/41750.html  :)
                    */
                        
                    $msgType = "text";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, "输入 @+影视名称 即可查看该影视的所有原声以及下载地址。");
                    echo $resultStr;
                    exit;  
                }              
                
                //原声点播-原声OST-文字类消息
                if($form_EventKey=="ysdb")
                {
                    $msgType = "text";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, "输入 #+歌名 或者 #+歌名+#+歌手 即可点播该原声歌曲(当然，不是所有的歌都有资源T.T)");
                    echo $resultStr;
                    exit;  
                }              
                
                //热门剧集-原声OST-文字类回复
                if($form_EventKey=="rmjj")
                {
                    $rmjj_list=$mysql->getData("select *
                                            from rmjj_tb
                                            where status = 1  
                                            order by rmjj_id desc 
                                            limit 0,1");
                    foreach($rmjj_list as $value)
                        {
                            //标题名
                            $rmjj_title = $value[rmjj_subject];
                            //url
                            $rmjj_url = $value[rmjj_options];
                            //描述
                            $rmjj_describe = $value[rmjj_true];
                        }


                    $msgType = "news";
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, time(), $msgType, 1,$rmjj_title,
                                         $rmjj_describe,
                                         "http://findost-findost.stor.sinaapp.com/%E7%83%AD%E9%97%A8%E5%89%A7%E9%9B%86index.jpg",
                                         $rmjj_url);
                    echo $resultStr;
                    exit;  
                }
          
                //今日导视-资讯资源-跳转网页 
                if($form_EventKey=="jrds")
                {
                    $jrds_list=$mysql->getData("select *
                                            from jrds_tb
                                            where status = 1  
                                            order by jrds_id desc 
                                            limit 0,1");
                    foreach($jrds_list as $value)
                        {
                            //标题名
                            $jrds_title = $value[jrds_subject];
                            //url
                            $jrds_url = $value[jrds_options];
                            //描述
                            $jrds_describe = $value[jrds_true];
                        }


                    $msgType = "news";
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, time(), $msgType, 1,$jrds_title,
                                         $jrds_describe,
                                         "http://findost-findost.stor.sinaapp.com/%E6%AF%8F%E6%97%A5%E5%AF%BC%E8%A7%86index.jpg",
                                         $jrds_url);
                    echo $resultStr;
                    exit;  
                }              
           
                //最新资讯-资讯资源-跳转网页
                if($form_EventKey=="zxzx")
                {
                     $zxzx_list=$mysql->getData("select *
                                            from zxzx_tb
                                            where status = 1  
                                            order by zxzx_id desc 
                                            limit 0,1");
                    foreach($zxzx_list as $value)
                        {
                            //标题名
                            $zxzx_title = $value[zxzx_subject];
                            //url
                            $zxzx_url = $value[zxzx_options];
                            //描述
                            $zxzx_describe = $value[zxzx_true];
                        }


                    $msgType = "news";
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, time(), $msgType, 1,$zxzx_title,
                                         $zxzx_describe,
                                         "http://findost-findost.stor.sinaapp.com/%E6%9C%80%E6%96%B0%E8%B5%84%E8%AE%AFindex.jpg",
                                         $zxzx_url);
                    echo $resultStr;
                    exit;  
                }  
                
                //剧集资源-咨询资源-跳转网页
                if($form_EventKey=="jjzy")
                {
                    
                    $msgType = "text";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, "跳转哪个字幕组资源网站啊。\n有没有要合作的字幕组啊？\n(= 。=)");
                    echo $resultStr;
                    exit; 
                }  
                
                //经典再现-咨询资源-跳转网页
                if($form_EventKey=="jdzx")
                {
                    $jdzx_list=$mysql->getData("select *
                                            from jdzx_tb
                                            where status = 1  
                                            order by jdzx_id desc 
                                            limit 0,1");
                    foreach($jdzx_list as $value)
                        {
                            //标题名
                            $jdzx_title = $value[jdzx_subject];
                            //url
                            $jdzx_url = $value[jdzx_options];
                            //描述
                            $jdzx_describe = $value[jdzx_true];
                        }


                    $msgType = "news";
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, time(), $msgType, 1,$jdzx_title,
                                         $jdzx_describe,
                                         "http://findost-findost.stor.sinaapp.com/%E7%BB%8F%E5%85%B8%E5%86%8D%E7%8E%B0index.jpg",
                                         $jdzx_url);
                    echo $resultStr;
                    exit;   
                }  
                
                //今日音乐-资讯资源-跳转网页
                if($form_EventKey=="jryy")
                {
                    $msgType = "news";
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, time(), $msgType, 1,"「今日音乐」",
                                         "「美剧插曲妞」自己的音乐盒，每日更新原声哟",
                                         "http://findost-findost.stor.sinaapp.com/%E4%BB%8A%E6%97%A5%E9%9F%B3%E4%B9%90index.jpg",
                                         "http://1.findost.sinaapp.com/playerweb/audio.html");
                    echo $resultStr; 
                    exit;
                }  
                
                //英语课堂一起玩儿-跳转网页
                if($form_EventKey=="yykt")
                {
                    $yykt_list=$mysql->getData("select *
                                            from yykt_tb
                                            where status = 1  
                                            order by yykt_id desc 
                                            limit 0,1");
                    foreach($yykt_list as $value)
                        {
                            //标题名
                            $yykt_title = $value[yykt_subject];
                            //url
                            $yykt_url = $value[yykt_options];
                            //描述
                            $yykt_describe = $value[yykt_true];
                        }


                    $msgType = "news";
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, time(), $msgType, 1,$yykt_title,
                                         $yykt_describe,
                                         "http://findost-findost.stor.sinaapp.com/%E8%8B%B1%E8%AF%AD%E8%AF%BE%E5%A0%82index.jpg",
                                         $yykt_url);
                    echo $resultStr;
                    exit; 
                }  
                
                
                //我要补充-一起玩儿-跳转网页
                if($form_EventKey=="wybc")
                {
                    $msgType = "news";
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, time(), $msgType, 1,"「我要补充」",
                                         "有啥想为「美剧插曲妞」补充的吗~~",
                                         "http://findost-findost.stor.sinaapp.com/%E6%88%91%E8%A6%81%E8%A1%A5%E5%85%85index.jpg",
                                         "http://mp.weixin.qq.com/s?__biz=MzA3NDczNjY5NQ==&mid=204937211&idx=1&sn=ffc7c1122af2abae329b3af2bf7fe1c7&scene=5#rd");
                    echo $resultStr; 
                    exit; 
                }  
                
                //商业合作-一起玩儿-跳转网页
                if($form_EventKey=="syhz")
                {
                    $msgType = "news";
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, time(), $msgType, 1,"「商业合作」",
                                         "有啥想跟「美剧插曲妞」合作的吗~~",
                                         "http://findost-findost.stor.sinaapp.com/%E5%95%86%E4%B8%9A%E5%90%88%E4%BD%9Cindex.jpg",
                                         "http://mp.weixin.qq.com/s?__biz=MzA3NDczNjY5NQ==&mid=204937211&idx=1&sn=ffc7c1122af2abae329b3af2bf7fe1c7&scene=5#rd");
                    echo $resultStr; 
                    exit; 
                } 
                
                
                

                //获取广告案例
                if($form_EventKey=="AD_1")
                {
                         
                    $msgType = "text";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType,"「美剧插曲妞」广告案例");
                    echo $resultStr;
                    exit;  
                }
                
            }
        }
        else 
        {
            echo "";
            exit;
        }
}
?>