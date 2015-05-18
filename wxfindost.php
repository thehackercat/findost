<?php
/*
 * 所属类：apiFunction
 * 函数名：baiduMusic()
 * 参数：
 * 功能：调用百度音乐api，推送音乐
 */

class apiFunction
{ 
  
  public function baiduMusic($Song, $Singer)
  {
    if (!empty($Song))
    {
      //音乐链接有两中品质，普通品质和高品质
      $music = array (
        'url' => "",
        'durl' => "");

      //采用php函数file_get_contents来读取链接内容
      $file = file_get_contents("http://box.zhangmen.baidu".".com/x?op=12&count=1&title=".$Song."$$".$Singer."$$$$");

      //simplexml_load_string() 函数把 XML 字符串载入对象中
      $xml = simplexml_load_string($file, 'SimpleXMLElement', LIBXML_NOCDATA);

      //如果count大于0,表示找到歌曲
      if ($xml->count > 0)
      {
        //普通品质音乐
        $encode_str = $xml->url->encode;

        //使用正则表达式，进行字符串匹配，处理网址
        preg_match("/http:\/\/([\w+\.]+)(\/(\w+\/)+)/", $encode_str, $matches);

        //第一个匹配的就是我们需要的字符串
        $url_parse = $matches[0];

        $decode_str = $xml->url->decode;

        //分离字符串，截去mid
        $decode_arr = explode('&', $decode_str);

        //拼接字符串,获得普通品质音乐
        $musicUrl = $url_parse.$decode_arr[0];


        //高品质音乐
        $encode_dstr = $xml->durl->encode;
        preg_match("/http:\/\/([\w+\.]+)(\/(\w+\/)+)/", $encode_dstr, $matches_d);

        //第一个匹配的就是我们需要的字符串
        $durl_parse = $matches_d[0];

        $decode_dstr = $xml->durl->decode;
        //分离字符串，截去mid
        $decode_darr = explode('&', $decode_dstr);

        //拼接字符串,获得高品质音乐
        $musicDurl = $durl_parse.$decode_darr[0];

        //将两个链接放入数组中
        $music = array(
          'url' => $musicUrl,
          'durl' => $musicDurl
        );
        return $music;

      }else{
      $music = "";
      return $music;
    	}
  	}
  }

}

  ?>