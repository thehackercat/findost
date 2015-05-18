<?php
/*****************************/
/*	author:zetd@vip.sina.com */
/*	date:2012/03/20          */
/*	wp-form-get              */
/*****************************/
/**
 * cgi输入获取类
 */
class cgi
{
	/**
	 * 以get方式取cgi变量
	 * @return mexid
	 * @param	string	$cgivv
	 *			string	$cgi_instr
	 *			integer	$defval
	 */
	static function get(&$cgivv, $cgi_instr, $defval = 0)
	{
		return cgi::input($cgivv, $cgi_instr, $defval, 0);
	}
	
	/**
	 * 以get方式取cgi变量
	 * @return mexid
	 * @param	string	$cgivv
	 *			string	$cgi_instr
	 *			integer	$defval
	 */
	static function post(&$cgivv, $cgi_instr, $defval = 0)
	{
		return cgi::input($cgivv, $cgi_instr, $defval, 1);
	}
	
	/**
	 * 以get方式取cgi变量
	 * @return mexid
	 * @param	string	$cgivv
	 *			string	$cgi_instr
	 *			integer	$defval
	 */
	static function both(&$cgivv, $cgi_instr, $defval = 0)
	{
		return cgi::input($cgivv, $cgi_instr, $defval, 2);
	}
	
	/**
	 * 取post方式的变量值
	 * @return mexid
	 * @param	string	$v	取值得名称
	 */
	static function _method_post($v)
	{
		if (isset($_POST[$v]))
		{
			return $_POST[$v];
		}
	}
	
	/**
	 * 取post方式的变量值
	 * @return mexid
	 * @param	string	$v	取值得名称
	 */
	static function _method_get($v)
	{
		if (isset($_GET[$v]))
		{
			return $_GET[$v];
		}
	}
	
	/**
	 * 取post方式如过不存在，取get方式的变量值
	 * @return mexid
	 * @param	string	$v	取值得名称
	 */
	static function _method_both($v)
	{
		if (isset($_POST[$v]))
		{
			return $_POST[$v];
		}
		else if (isset($_GET[$v]))
		{
			return $_GET[$v];
		}
	}

	/**
	 * CGI变量接收
	 * @return mexid
	 * @param	string	$cgivv
	 *			string	$cgi_instr
	 *			integer	$defval
	 */
	static function input(&$cgivv, $cgi_instr, $defval, $cgitype)
	{
		$cgi_in = NULL;
		switch($cgitype)
		{
			case 1:
				$cgi_in = cgi::_method_post($cgi_instr);
				break;
			case 2:
				$cgi_in = cgi::_method_both($cgi_instr);
				break;
			default:
				$cgi_in = cgi::_method_get($cgi_instr);
				break;
		}
		
		if (is_null($cgi_in) or $cgi_in == '')
		{
			if (is_numeric($cgivv))
			{
				$cgivv = $defval + 0;
			}
			else
			{
				$cgivv = $defval . '';
			}
			return false;
		}
		else
		{
			if (is_numeric($defval))
			{
				if (!is_numeric($cgi_in))	// 如果要求是数值，而传入是非数值
				{
					$cgivv	= $defval + 0;
					return false;
				}
			}
			$cgivv	= $cgi_in;
			return true;
		}
	}
}


Class string
{
	function string()
	{
		/****/
	} 
	/**
	 * 判断字符某个位置是中文字符的左半部分还是右半部分，或不是中文
	 * 返回 0表示在中文3号位置，1表示在中文1号位置，2表示在2号位置,-1表示不是中文
     * @return int
	 * @param string $str 开始位置
	 * @param int $location 位置
	 */
	
	function is_cn_utf8_str($str, $location)
	{
		$cut_location=$location+1;
		$length = $location;
		if($location < 0 )return 1;
		if(ord($str{$length}) <= 127)return -1;
					
		 for($i = $length ;$i >=0; $i--)
		 {
		 	
		 	 if(ord($str{$i}) <= 127)
		 	 {
		 	 	$cut_location=$location-$i;
		 	 	break;
		 	 }
		 	
		 }
		 return $cut_location%3 ==0 ? 3 : $cut_location%3 ;
	}
		
	/**
	 * 处理截取中文字符串的操作
     * @return string
     * @param string $str 要处理的字符
	 * 		  string $start 开始位置
	 *        string $offset 偏移量
	 *        string $t_str 字符结果尾部增加的字符串，默认为空
	 *        boolen $ignore $start位置上如果是中文的某个字后半部分是否忽略该字符，默认true
	 */
	function substr_cn_utf8($str, $start, $offset, $t_str = '', $ignore = true)
	{
	 	$length  = strlen($str);
		if ($length <=  $offset && $start == 0)
		{
			return $str;
		}
		if ($start > $length)
		{
			return $str;
		}
		$r_str     = "";
		for ($i = $start; $i < ($start + $offset); $i++)
		{ 
			if (ord($str{$i}) > 127)
			{
				if ($i == $start)  //检测头一个字符的时候，是否需要忽略半个中文
				{
					$cut_length = string::is_cn_utf8_str($str, $i);
					
					if ( $cut_length!= -1)
					{
						if ($ignore && ($cut_length==2 || $cut_length==3 ))
						{
							$i=$i+(3-$cut_length);
							
							continue;
						}
						else
						{
							$i=$i - $cut_length + 1;
						
							$r_str .= $str{($i)}.$str{++$i}.$str{++$i};
							
						
						}
					}
					else
					{
						$r_str .= $str{$i};
					}
					
				}
				else
				{
					
					$r_str .= $str{$i}.$str{++$i}.$str{++$i};
				}
			}
			else
			{
				
				$r_str .= $str{$i};
				continue;
			}
			
		}
		
		return $r_str . $t_str;
		//return preg_replace("/(&)(#\d{5};)/e", "string::un_html_callback('\\1', '\\2')", $r_str . $t_str);
		
	}

	function substr_cn($str, $start, $offset, $t_str = '', $ignore = true)
	{
	 	$length  = strlen($str);
		if ($length <=  $offset && $start == 0)
		{
			return $str;
		}
		if ($start > $length)
		{
			return $str;
		}
		$r_str     = "";
		for ($i = $start; $i < ($start + $offset); $i++)
		{ 
			if (ord($str{$i}) > 127)
			{
				if ($i == $start)  //检测头一个字符的时候，是否需要忽略半个中文
				{
					if (string::is_cn_str($str, $i) == 1)
					{
						if ($ignore)
						{
							continue;
						}
						else
						{
							$r_str .= $str{($i - 1)}.$str{$i};
						}
					}
					else
					{
						$r_str .= $str{$i}.$str{++$i};
					}
				}
				else
				{
					$r_str .= $str{$i}.$str{++$i};
				}
			}
			else
			{
				$r_str .= $str{$i};
				continue;
			}
		}
		return $r_str . $t_str;
		//return preg_replace("/(&)(#\d{5};)/e", "string::un_html_callback('\\1', '\\2')", $r_str . $t_str);
		
	}
	
	/**
	function un_html_callback($a, $b){
        	if ($b){
                	return $a. $b;
        	}
        	return '&amp;';
	}
	**/
	
	//-- 判断字符串是否含有非法字符 -------
	function check_badchar($str, $allowSpace = false)
	{
		if ($allowSpace)
			return preg_match ("/[><,.\][{}?\/+=|\\\'\":;~!@#*$%^&()`\t\r\n-]/i", $str) == 0 ? true : false;
		else
			return preg_match ("/[><,.\][{}?\/+=|\\\'\":;~!@#*$%^&()` \t\r\n-]/i", $str) == 0 ? true : false;
	}
	
	/**
	 * 判断字符某个位置是中文字符的左半部分还是右半部分，或不是中文
	 * 返回 1 是左边 0 不是中文 -1是右边
     * @return int
	 * @param string $str 开始位置
	 * @param int $location 位置
	 */
	 
	function is_cn_str($str, $location)
	{ 
		$result	= 1;
		$i		= $location;
		while(ord($str{$i}) > 127 && $i >= 0)
		{ 
			$result *= -1; 
			$i --; 
		} 
		
		if($i == $location)
		{ 
			$result = 0; 
		} 
		return $result; 
	} 
	
	/**
	 * 判断字符是否全是中文字符组成
	 * 2 全是 1部分是 0没有中文
     * @return boolean
	 * @param string $str 要判断的字符串
	 */
	 
	function chk_cn_str($str)
	{ 
		$result = 0;
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++)
		{
			if (ord($str{$i}) > 127)
			{
				$result ++;
				$i ++;
			}
			elseif ($result)
			{
				$result = 1;
				break;
			}
		}
		if ($result > 1)
		{
			$result = 2;
		}
		return $result;
	} 
	
	/**
	 * 判断邮件地址的正确性
	 * @return boolean
	 * @param string $mail 邮件地址
	 */
	
	function is_mail($mail)
	{
		//return preg_match("/^[a-z0-9_\-\.]+@[a-z0-9_]+\.[a-z0-9_\.]+$/i" , $mail);
		return preg_match('/^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+){1,4}$/', $mail) ? true : false;
	}
	
	/**
 * 验证输入的手机号码
 *
 * @access  public
 * @param   string      $user_mobile      需要验证的手机号码
 *
 * @return bool
 */
function is_mobile($mobile)
{

	return preg_match("/^((\(\d{2,3}\))|(\d{3}\-))?1(3|5|8|9)\d{9}$/", $mobile)? true : false;
}

	
	
	
	/**
	 * 判断App的CallbackURL是否合法（可以包含端口号）
	 * @return boolean
	 * @param string $url URL地址
	 */
	
	function is_callback_url($url)
	{
		return  preg_match("/(ht|f)tp(s?):\/\/([\w-]+\.)+[\w-]+(\/[\w-.\/?%&=]*)?/i" , $url);
	}
	
	/**
	 * 判断URL是否以http(s):// ftp://格式开始的地址
	 * @return boolean
	 * @param string $url URL地址
	 */
	
	function is_http_url($url)
	{
		return  preg_match("/^(https?|ftp):\/\/([\w-]+\.)+[\w-]+(\/[\w;\/?:@&=+$,# _.!~*'\"()%-]*)?$/i" , $url);
		//return preg_match("/^(http(s)|ftp):\/\/[a-z0-9\.\/_-]*?$/i" , $url);
	}
	
	/**
	 * 允许中文
	 */
	function is_url($url)
	{
		//return  preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[\w;\/?:@&=+$,# _.!~*'\"()%-]*)?$/i" , $url);
		//return  preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i" , $url);
		//return preg_match("/^(http(s)|ftp):\/\/[a-z0-9\.\/_-]*?$/i" , $url);
		return preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(:\d{1,9}+)?(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i" , $url);

	}

	/**
	 * 判断URL是否是正确的音乐地址
	 * @return boolean
	 * @param string $url URL地址
	 */
	
	function is_music_url($url)
	{
		return preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(:\d{1,9}+)?(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i" , $url);
		//return preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i" , $url);
		//return  preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[\w;\/?:@&=+$,# _.!~*'\"()%-]*)?$/i" , $url);
		//return preg_match("/^(http(s)|ftp):\/\/[a-z0-9\.\/_-]*?$/i" , $url);
	}

		

	/**
	 * 过滤字符串中的特殊字符
	 * @return string
	 * @param string $str 需要过滤的字符
	 * @param string $filtStr 需要过滤字符的数组（下标为需要过滤的字符，值为过滤后的字符）
	 * @param boolen $regexp 是否进行正则表达试进行替换，默认false
	 */
	
	function filt_string($str, $filtStr, $regexp = false)
	{
		if (!is_array($filtStr))
		{
			return $str;
		}
		$search		= array_keys($filtStr);
		$replace	= array_values($filtStr);
				
		if ($regexp)
		{
			return preg_replace($search, $replace, $str);
		}
		else
		{
			return str_replace($search, $replace, $str);
		}
	}
	
	/**
	 * 过滤字符串中的HTML标记 < >
	 * @return string
	 * @param string $str 需要过滤的字符
	 */
	
	function un_html($str)
	{
			$s	= array(
				"&"     => "&amp;",
				"<"	=> "&lt;",
				">"	=> "&gt;",
				"\n"	=> "<br>",
				"\t"	=> "&nbsp;&nbsp;&nbsp;&nbsp;",
				"\r"	=> "",
				" "	=> "&nbsp;",
				"\""	=> "&quot;",
				"'"	=> "&#039;",
			);
		//$str = string::esc_korea_change($str);
		$str = strtr($str, $s);
		//$str = string::esc_korea_restore($str);
		return $str;
	}
	
	/**
	 * 过滤字符串的特殊字符，以便把数据存入mysql数据库
	 */
	function esc_mysql($str)
	{
		return mysql_escape_string($str);
	}

	/**
	 * 过滤字符串的特殊字符，以便把数据输出到页面做编辑显示
	 */
	function esc_edit_html($str)
	{
		$s	= array(
			//"&"     => "&amp;",
			"<"		=> "&lt;",
			">"		=> "&gt;",
			"\""	=> "&quot;",
			"'"		=> "&#039;",
		);
		$str = string::esc_korea_change($str);
		$str = strtr($str, $s);
		$str = string::esc_korea_restore($str);        
		return $str;
	}


	/**
	 * 过滤字符串的特殊字符，以便把数据输出到页面做输出显示
	 */
	function esc_show_html($str)
	{
		$s	= array(
			"&"     => "&amp;",
			"<"		=> "&lt;",
			">"		=> "&gt;",
			"\n"	=> "<br>",
			"\t"	=> "&nbsp;&nbsp;&nbsp;&nbsp;",
			"\r"	=> "",
			" "		=> "&nbsp;",
			"\""	=> "&quot;",
			"'"		=> "&#039;",
		);
		
		
		$str = string::esc_korea_change($str);
		$str = strtr($str, $s);
		$str = string::esc_korea_restore($str);
		return $str;
	}
	
       
	function esc_ascii($str)
	{
		$esc_ascii_table = array(
   	    	chr(0),chr(1), chr(2),chr(3),chr(4),chr(5),chr(6),chr(7),chr(8),
   		    chr(11),chr(12),chr(14),chr(15),chr(16),chr(17),chr(18),chr(19),
      		chr(20),chr(21),chr(22),chr(23),chr(24),chr(25),chr(26),chr(27),chr(28),
        	chr(29),chr(30),chr(31)
		);


		$str = str_replace($esc_ascii_table, '', $str);
		return $str;
	}

	function esc_user_input($str)
	{
		//$str = iconv("utf-8", "gb2312", $str);
		$str = iconv("utf-8", "gbk//IGNORE", $str);
		// 过滤非法词汇

		// 过滤非法ASCII字符串
		$str = string::esc_ascii($str);

		// 过滤SQL语句	
		//$str = string::esc_mysql($str);
		

		return $str;
	}
	
	/**
	 * 过滤字符串中的<script ...>....</script>
	 * @return string
	 * @param string $str 需要过滤的字符
	 */
	 
	function un_script_code($str)
	{
		$s			= array();
		$s["/<script[^>]*?>.*?<\/script>/si"] = "";
		return string::filt_string($str, $s, true);
	}
	
	/**
	 * 把HTML代码转化ducument.write输出的内容
	 * @return string
	 * @param string $html 需要处理的HTML代码
	 */
	 
	function html2script($html)
	{
		//需要进行转义的字符
		$s			= array();
		$s["\\"]	= "\\\\";
		$s["\""]	= "\\\"";
		$s["'"]		= "\\'";
		$html = string::filt_string($html, $s);
		$html = implode("\\\r\n", explode("\r\n", $html));
		
		return "document.write(\"\\\r\n" . $html . "\\\r\n\");";
	}
	
	// 转义js输出，返回合法的js字符串
	function js_esc($str)
	{
		$s_tag = array("\\", "\"", "/", "\r", "\n");
		$r_tag = array("\\\\", "\\\"", "\/", "\\r", "\\n");
		$str = str_replace($s_tag, $r_tag, $str);

		return $str;
	}

	/**
	 * 把ducument.write输出的内容转化成HTML代码(必须是html2script函数进行转化的结果)
	 * @return string
	 * @param string $jsCode 需要处理的JS代码
	 */
	 	 
	function script2html($jsCode)
	{
		$html = explode("\\\r\n", $jsCode);
		array_shift($html);		//去掉数组开头单元
		array_pop($html);		//去掉数组末尾单元
		return implode("\r\n", $html);
	}

	static function length($str)
	{
		$str = preg_replace("/&(#\d{5});/", "__", $str);
		return strlen($str);
	}
	
}



//获取IP地址
function GetIP()
{
	if(!empty($_SERVER["HTTP_CLIENT_IP"]))
	   $cip = $_SERVER["HTTP_CLIENT_IP"];
	else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
	   $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else if(!empty($_SERVER["REMOTE_ADDR"]))
	   $cip = $_SERVER["REMOTE_ADDR"];
	else
	   $cip = "";
	return $cip;
}


//翻页

//分页
function multi($num, $perpage, $curpage, $mpurl, $ajax=0, $ajax_f='',$flag='') {


	$page = 5;
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&' : '?';
	$realpages = 1;
	if($num > $perpage) {
		$offset = 2;
		$realpages = @ceil($num / $perpage);
		$pages = $realpages;
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		$multipage = '';
		if($curpage - $offset > 1 && $pages > $page) {
			$multipage .= "<a ";
			if($ajax) {
				$multipage .= "href=\"javascript:{$ajax_f}($flag,1);\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=1{$urlplus}\"";
			}
			$multipage .= " class=\"first\">首</a>";
		}
		if($curpage > 1) {
			$multipage .= "<a ";
			if($ajax) {
				$multipage .= "href=\"javascript:{$ajax_f}($flag,".($curpage-1).");\" ";
			} else {
				$multipage .= "href=\"{$mpurl}page=".($curpage-1)."$urlplus\"";
			}
			$multipage .= " class=\"prev\">&lt;&lt; </a>";
		}
		for($i = $from; $i <= $to; $i++) {
			if($i == $curpage) {
				$multipage .= '<a href="###" class="cur">'.$i.'</strong>';
			} else {
				$multipage .= "<a ";
				if($ajax) {
					$multipage .= "href=\"javascript:{$ajax_f}($flag,$i);\" ";
				} else {
					$multipage .= "href=\"{$mpurl}page=$i{$urlplus}\"";
				}
				$multipage .= ">$i</a>";
			}
		}
		if($curpage < $pages) {
			$multipage .= "<a ";
			if($ajax) {
				$multipage .= "href=\"javascript:{$ajax_f}($flag,".($curpage+1).");\" ";
			} else {
				$multipage .= "href=\"{$mpurl}page=".($curpage+1)."{$urlplus}\"";
			}
			$multipage .= " class=\"next\"> &gt;&gt;</a>";
		}
		if($to < $pages) {
			$multipage .= "<a ";
			if($ajax) {
				$multipage .= "href=\"javascript:{$ajax_f}($flag,$pages);\" ";
			} else {
				$multipage .= "href=\"{$mpurl}page=$pages{$urlplus}\"";
			}
			$multipage .= " class=\"last\">尾</a>";
		}
		if($multipage) {
			//$multipage = '<em>&nbsp;'.$num.'&nbsp;</em>'.$multipage;
		}
	}
	return $multipage;
}

?>
