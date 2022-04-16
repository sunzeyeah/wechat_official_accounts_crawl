<?php
//getWxPost.php 当前页面为公众号文章页面时，读取这个程序
//首先删除采集队列表中load=1的行
//然后从队列表中按照“order by id asc”选择多行(注意这一行和上面的程序不一样)
$serve = 'localhost:3306';
$username = 'weiting';
$password = '12w34567';
$dbname = 'weixin';
$link = mysqli_connect($serve,$username,$password,$dbname);
mysqli_set_charset($link,'UTF-8'); // 设置数据库字符集


$str = $_REQUEST['str'];
$url = $_REQUEST['url'];//先获取到两个POST变量
$content_url_query = array();
parse_str(parse_url(htmlspecialchars_decode(urldecode($url)),PHP_URL_QUERY ),$content_url_query);//解析url地址
$sn = $content_url_query['sn'];
$biz = $content_url_query['__biz'];


$html = urldecode($str);


//$html = file_get_contents("./weixin_source_html/f7d2d1a4c9b48ddbd2ac192c109605ab.html");
preg_match_all('/<div class="rich_media_content " id="js_content" style="visibility: hidden;">([\s\S]*?)<\/div>/iUs',$html,$content,PREG_PATTERN_ORDER);
//preg_match_all('/<div(.*)<\/div>/U',$html,$content,PREG_PATTERN_ORDER);
$content = $content[0][0];
$content = str_replace(array("\r\n", "\r", "\n"), "", $content);
$content = str_replace("visibility: hidden;","",$content);
$content = str_replace("data-src","src",$content);
$content = str_replace("preview.html","player.html",$content);
$content_html = str_replace(PHP_EOL, '', str_replace("wx_fmt","tp=webp&wx_fmt",$content));
$content = strip_tags($content);

//$content_html = mysqli_real_escape_string($link,$content);
$content_html = addslashes($content_html);
$content = addslashes($content);
//$content = str_replace(" ","",$content);

//var_dump($content_html);

$sql = "select * from post where sn='$sn'";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);  
$title = $row['title'];

//$sql = "update post set content='$content' where sn like '$sn'";
//$sql = "update post set content_html='$content_html', content='$content' where sn like '$sn'";
$sql = "update post set content_html='$content_html', content='$content',craw=1 where biz='$biz' and title='$title'";


//var_dump($sql);
//return;
/***
$debugfile = fopen("sql.txt", "w");
fwrite($debugfile, $sql);
fclose($debugfile);***/
$result = mysqli_query($link, $sql);

preg_match_all('/var nickname = \"(.*?)\";/si',$html,$m);
$nickname = $m[1][0];//公众号昵称
preg_match_all('/var round_head_img = \"(.*?)\";/si',$html,$m);
$head_img = $m[1][0];//公众号头像

if(!empty($nickname) && !empty($head_img))
{
	$sql = "update weixin set nickname='$nickname', head_img='$head_img' where biz like '$biz'";
	$result = mysqli_query($link, $sql);
}
/***
$debugfile = fopen("./weixin_source_html/$sn.html", "w");
fwrite($debugfile, $html);
fclose($debugfile);

var_dump($content_html);
***/
?>


