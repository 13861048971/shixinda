<?php
/**
 * 常用函数
 */
/*
 * 身份证号验证(兼容15，18位)
 * 返回数组 status = 0;
 */
function isIdCardNo($idcard) {
    $return = array('status'=>0, 'msg'=>'');
     if( empty($idcard) ){
        $return['msg'] = '输入的身份证号码不能够为空';
        return $return;
    }
    $City = array(11=>"北京",12=>"天津",13=>"河北",14=>"山西",15=>"内蒙古",21=>"辽宁",22=>"吉林",23=>"黑龙江",31=>"上海",32=>"江苏",33=>"浙江",34=>"安徽",35=>"福建",36=>"江西",37=>"山东",41=>"河南",42=>"湖北",43=>"湖南",44=>"广东",45=>"广西",46=>"海南",50=>"重庆",51=>"四川",52=>"贵州",53=>"云南",54=>"西藏",61=>"陕西",62=>"甘肃",63=>"青海",64=>"宁夏",65=>"新疆",71=>"台湾",81=>"香港",82=>"澳门",91=>"国外");
    $iSum = 0;
    $idCardLength = strlen($idcard);
    //长度验证
    if(!preg_match('/^\d{17}(\d|x)$/i',$idcard) and!preg_match('/^\d{15}$/i',$idcard)) {
        $return['msg'] = L('身份证长度错误', array('length1'=>'15', 'length2'=>'17'));
        return $return;
    }
    //地区验证
    if(!array_key_exists(intval(substr($idcard,0,2)),$City)) {
        $return['msg'] = '身份证号码的地区编号错误';
        return $return;
    }
    // 15位身份证验证生日，转换为18位
    if ($idCardLength == 15) {
        $sBirthday = '19'.substr($idcard,6,2).'-'.substr($idcard,8,2).'-'.substr($idcard,10,2);
        $d = strtotime($sBirthday);
        $dd = date('Y-m-d', $d);
        if($sBirthday != $dd) {
            $return['msg'] = '身份证号码的生日错误';
            return $return;
        }
        $idcard = substr($idcard,0,6)."19".substr($idcard,6,9);//15to18
        $Bit18 = getVerifyBit($idcard);//算出第18位校验码
        $idcard = $idcard.$Bit18;
    }
    // 判断是否大于2078年，小于1900年
    $year = substr($idcard,6,4);
    if ($year<1900 || $year>2078 ) {
        $return['msg'] = '身份证号码的出生年份错误';
        return $return;
    }
    //18位身份证处理
    $sBirthday = substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
    $d = strtotime($sBirthday);
    $dd = date('Y-m-d', $d);
    if($sBirthday != $dd) {
        $return['msg'] = '身份证号码的出生年月日错误';
        return $return;
    }
    //身份证编码规范验证
    $idcard_base = substr($idcard,0,17);
    if(strtoupper(substr($idcard,17,1)) != getVerifyBit($idcard_base)) {
        $return['msg'] = '身份证编码不符合规范验证';
        return $return;
    }
    $return['status'] = 1;
    return $return;
}
// 计算身份证校验码，根据国家标准GB 11643-1999
function getVerifyBit($idcard_base) {
    if (strlen($idcard_base) != 17) {
        return false;
    }
    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4','3', '2');
    $checksum = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}
/**
 * 格式化时间差
 */
function timediff( $begin_time, $end_time )
{
  if ( $begin_time < $end_time ) {
    $starttime = $begin_time;
    $endtime = $end_time;
  } else {
    $starttime = $end_time;
    $endtime = $begin_time;
  }
  $timediff = $endtime - $starttime;
  $days = intval( $timediff / 86400 );
  $remain = $timediff % 86400;
  $hours = intval( $remain / 3600 );
  $remain = $remain % 3600;
  $mins = intval( $remain / 60 );
  $secs = $remain % 60;
  $res = array( "day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs );
  return $res;
}
/**
 * 过滤二维数组
 * @param array $arr
 * @param string $keyStr
 * @param bool $include
 * @return $arr
 */ 
function filter($arr, $keyStr, $include = true){
	$keyArr = explode(',', $keyStr);
	
	if(!$keyArr) return $keyArr;
	
	foreach($arr as $k1=>$v1){
		foreach($v1 as $k=>$v){
			if($include && !in_array($k, $keyArr)){
				unset($v1[$k]);
			}
			
			if(!$include && in_array($k, $keyArr)){
				unset($v1[$k]);
			}
		}

		$arr[$k1] = $v1;
	}
	return $arr;
}
 
/**
 * 设置数组选中
 * @param array $list
 * @param mix $key
 * @param mix $value
 */
function setActive(&$list, $key, $value){
	if(!isset($value) || '' === $value) return;
	foreach($list as &$v){
		if($v[$key] == $value){
			$v['active'] = true;
			return;
		}
	}
} 
/**
 * 字符串中下划线转换成驼峰
 * @param string $str
 * @param boolean $ucfirst 
 * @return string
 */
function underline2hump($str, $ucfirst = true){
	$arr = explode('_', $str);
	foreach($arr as $v){
		if($v){
			$str2 .= ucfirst($v);
		}
	}
	if($ucfirst) 
		return $str2;
	return lcfirst($str);
}
/**
 * 快速文件数据读取和保存 针对简单类型数据 字符串、数组
 * @param string $name 缓存名称
 * @param mixed $value 缓存值
 * @param string $path 缓存路径
 * @return mixed
 */
function F2($name, $value='', $path=DATA_PATH) {
     $filename       = $path . $name . '.php';
     if ('' !== $value) {
         if (is_null($value)) {
             return unlink($filename);
        } else {
             $dir = dirname($filename);
             // 目录不存在则创建
             if (!is_dir($dir))
                 mkdir($dir,0777,true);
             return file_put_contents($filename, serialize($value));
         }
     }

     if (is_file($filename)){
         $value =   unserialize(file_get_contents($filename));
     } else {
         $value = false;
     }
     return $value;
}
 
/**
 * 创建(3,9) 这样字符串 
 * @param array $ids
 * @return string or bool 失败返回false
 */
function inStr($ids){
	if(!$ids && !is_array($ids))
		return false;
	foreach($ids as $v){
		if(is_numeric($v)) $str .= $v . ',';
		else
			$str .= "'$v',";
	}
	
	return '(' . rtrim($str, ',') . ')';
}
 
/**
 * 二维数组查找
 * @param array $arr
 * @param mix	$key
 * @param mix	$value
 * @return key || boolean 
 */
function arr2Search($arr, $key, $value){
	foreach($arr as $k=>$v){
		if($v[$key] == $value)
			return $k;
	}
	
	return false;
}
/**
 * 格式的时间函数
 */
function local_date($t , $format = 'Y.m.d H:i:s', $friendly = true){
	$now = time();
	if(!isset($t)){ 
		return date($format, $now);
	}
	if(!$t) return;
	if(!$friendly)
		return date($format, $t);
	
	$year = date('Y',$t);
	$thisYear = date('Y');
	$nextYear = $thisYear + 1;
	
	static $today;
	$today = mktime(0,0,0,date('m'),date('d'), $thisYear); 
	$day = mktime(0,0,0,date('m',$t),date('d',$t), $year);
	$cha = $today - $day;
	
	if($cha > 0 && $cha < 24*3600){
		return date('H:i', $t);
	}
	if($cha > 0 && $cha < 2*24*3600){
		return '昨日'.date('H:i', $t);
	}
	if($cha < 0 &&  $cha > -24*3600-1){
		return '明日'.date('H:i', $t);
	}
	
	if( $year == $thisYear)
		return date('n月j日 H:i', $t);
	return date($format, $t);
}

 
 /**
  * 过滤html 标签
  */
function triptag($str, $tag){
	$tag1 = '<' . $tag;
	$tag2 = "</$tag>";

	while(false !== ($i = strpos($str, $tag1))){
		$j = strpos($str, $tag2);
		if( $j<= $i ) break;
		$str = substr_replace($str, '', $i, $j-$i + strlen($tag2));
	}
	return $str;
}
  
/**
 * 查找标签
 * @param string $str
 * @param string $taget 查找的标签开始处 eq: <div class="sdsds" 
 * @param string $tag 标签的名称
 */
function findHtml($str, $taget, $tag){
	$tag1 = '<' . $tag;
	$tag2 = "</$tag>";
	$l = strpos($str, $taget);
	
	$str1 = substr($str, 0, $l+1);
	$str2 = substr($str, $l);
	$i = 0;
	
	$len1 = strlen($tag1);
	$len2 = strlen($tag2);
	//标签开始位置
	$star = strrpos($str1, $tag1);
	if($star < 0) return false;
	
	$i = $j = 0;
	$n = 1;
	//标签结束位置
	while($n>0 && false !== ($i = strpos($str2, $tag2, $end))){
		$n--;
		
		$str3 = substr($str2, $end, $i - $end);
		while(false !== ($j = strpos( $str3, $tag1, $j))){
			$j += $len1;
			$n++;
		}
		$end = $i + $len2;
	}
	$end = $end+$l - $star;
	return substr($str, $star, $end);
}

/**
 * 模仿浏览器头 get
 * @param string $url
 * @param string $headRow
 */
function httpGet($url, $headRow=''){
	$header = "Accept:text/javascript, application/javascript, application/ecmascript, application/x-ecmascript, */*; q=0.01\r\n".
	  "Cache-Control:no-cache\r\n".
	  "Pragma:no-cache\r\n".
	  "Accept-language: zh-CN,zh;q=0.8\r\n" . 
	  //"Referer:http://flowaid.cc/flow/taskAdd\r\n".
	  "upgrade-insecure-requests:1\r\n".
	  "cookie:thw=cn; cna=IFjzDBB+GmQCAWVpzmw4qmJZ; v=0; cookie2=1c2c7fd1d54adbd18608ca3c7ba94d29; t=652b563c5a4831a0d559d118c12b6675; mt=ci%3D-1_1; l=AsbGryIlGUyVIRe/T0Q0Rp4AlrZIIArh; isg=D9A3722D03CE494D7B2F0871FB083CAC\r\n".
	  "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2552.0 Safari/537.36";
	if($headRow) $header .= $headRow;
	$opts = array(
		'http'=>array(
			'method'=> "GET",
			'header'=> $header
		)
	);
	return file_get_contents($url, false, stream_context_create($opts));
}

/**
 * 发送HTTP请求方法
 * @param  string $url    请求URL
 * @param  array  $params 请求参数
 * @param  string $method 请求方法GET/POST
 * @return array  $data   响应数据
 */
 function chttp($url, $params, $method = 'GET', $header = array(), $multi = false){
    $opts = array(
		CURLOPT_TIMEOUT        => 30,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_HTTPHEADER     => $header
    );
    /* 根据请求类型设置特定参数 */
    switch(strtoupper($method)){
        case 'GET':
            $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            break;
        case 'POST':
            //判断是否传输文件
            $params = $multi ? $params : http_build_query($params);
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            throw new Exception('不支持的请求方式！');
    }
    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) throw new Exception('请求发生错误：' . $error);
    return  $data;
 }

/**
 * 输出ajax数据
 * @param int 	 $error 错误码 0 表示没有错误
 * @param string $msg 返回消息
 * @param array  $data 返回数据 
 * @param array  $append 追加的数据
 **/
function ajaxReturn($error = 1, $msg = '', $data = null ,$append = null){
	$arr = array('error'=>$error,'info'=>$msg);
	$arr['status'] = 0;
	!$error ? $arr['status'] = 1 : null;
	if(is_array($data)) $arr['data'] = $data;
	if($append && is_array($append)) 
		$arr = array_merge($append, $arr);
	
	die(json_encode($arr,JSON_UNESCAPED_UNICODE));
}

/**
 * 输出ajax数据
 * @param int 	 $error 错误码 0 表示没有错误
 * @param string $msg 返回消息
 * @param array  $data 返回数据 
 * @param array  $append 追加的数据
 **/
function ajaxReturn2($error = 1, $msg = '', $data = null ,$append = null){
	$arr = array('error'=>$error,'info'=>$msg, 'sid'=>session_id());
	if(is_array($data)) $arr = array_merge($data, $arr);
	if($append && is_array($append)) 
		$arr = array_merge($append, $arr);
	
	die(json_encode($arr,JSON_UNESCAPED_UNICODE));
}
  
/**
 * 查找标签内的内容
 * @param string $str 
 * @param string $taget 查找的标签开始处 eq: <div class="sdsds" 
 * @param string $tag 标签的名称
 * @return array [0:content,2:offset]
 */
function getTagInfo($str, $tag1, $tag2, $offset = 0){
	$len = strlen($tag1);
	$len2 = strlen($tag2);
	$star = strpos($str,$tag1, $offset);
	if(false === $star) return;
	$offset1  = $offset2 = $star + $len;
	$n = 1;
	$loop = 0;
	while(true){
		$loop++;
		$end  = strpos($str,$tag2, $offset2);
		if(false !== $end){
			$n--;
		}
		$pos1 = strpos($str,$tag1, $offset1);
		if(false === $pos1 || $pos1 > $end || $loop>1000) 
			break;
		
		if(false !== $pos1){
			$n++;
		}
		$offset1 = $pos1 + $len;
		$offset2 = $end + $len2;
	}
	
	return [substr($str, $star, $end - $star + $len2), $end ];
}
/**
 * 解析 session 文件""的值
 * @param string $s
 * @return array;
 */
function parseSessionStr($s){
	preg_match_all('/[^|\{\}\"]+\|+[\d\:\w]+\{/', $s, $match);
	if(!$match[0]) return;
	foreach($match[0] as $k=>$v){
		$pos = strpos($s, $v);
		$len = strlen($pos);
		$arr = explode('|', $v);
		$key = $arr[0];
		$str = getTagInfo($s, '{', '}', $pos + $len -1);
		$sess[$key] = unserialize(rtrim($arr[1],'{').$str[0]);
	}
	return $sess;
}

/**
 * 二维数组排序
 */
function arrSort($arr, $key, $order='asc'){
	$attr = [];
	
	foreach($arr as $k=>$v){
		$attr[] = $v[$key];
	}
	$order == 'asc' ?  $order = SORT_ASC : $order = SORT_DESC; 
	array_multisort($attr, $order, $arr);
	return $arr;
}

/**
 * 获取父类
 * @param string $class
 * @return []
 */
function getParents($class){
	$pArr = [];
	while($class = get_parent_class($class)){
		$pArr[] = $class;
	}
	return $pArr;
}
/**
 * 判断是否是某个类的子类
 * @param object|string $child
 * @param string $pClass
 **/
function isChild($child, $pClass){
	$pArr = getParents($child);
	if(!in_array($pClass, $pArr)) 
		return false;
	
	return true;
}

/**
 * 获取图片缩略图,or 中型图
 * @param string $src 图片路径
 * @param int	 $type 1.缩略图 2.中型图
 */
function getImage($src, $type = 1){
	$type =  1 == $type ? '_thumb.' : '_medium.';
	$arr = explode('.', $src);
	$ext = end($arr);
	return str_replace('.' . $ext, $type.$ext, $src);
}

/**
 * 字符串截取，支持中文和其他编码
 * static 
 * access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
	$len = strlen($str);
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
	
	if($suffix && strlen($slice) < $len){
		return $slice.'...'; 
	} 
    return $slice;
}