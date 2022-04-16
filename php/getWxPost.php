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


$sql = 'select id,content_url,biz from post where craw=0';

$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result); 

//if(!empty($row) && count('队列表中的行数')>1){//(注意这一行和上面的程序不一样)
if(!empty($row)){
	//取得第0行的content_url字段
	$tmplist_id = $row['id'];
	$url = $row["content_url"];
	$id  = $row["id"];
	//将第0行的load字段update为1
	$sql = "update post set craw=1 where id=$tmplist_id";
	$result = mysqli_query($link, $sql);
}else{
	//队列表还剩下最后一条时，就从存储公众号biz的表中取得一个biz，这里我在公众号表中设置了一个采集时间的time字段，按照正序排列之后，就得到时间戳最小的一个公众号记录，并取得它的biz
	$sql = "select * from weixin order by collect asc limit 1";
	$result = mysqli_query($link, $sql);
	$row = mysqli_fetch_assoc($result); 
	$biz = $row['biz'];
	
	$url = "http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=".$biz."#wechat_webview_type=1&wechat_redirect";//拼接公众号历史消息url地址（第一种页面形式）
	$url = "https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=".$biz."&scene=124#wechat_redirect";//拼接公众号历史消息url地址（第二种页面形式）
	//更新刚才提到的公众号表中的采集时间time字段为当前时间戳。
	$collect = time();
	$sql = "update weixin set collect=$collect where biz like '$biz'";
	$result = mysqli_query($link, $sql);
	
}	/***
	$debugfile = fopen("debug_mp_profile_ext_action=home.txt", "a+");
	fwrite($debugfile, $url ."\n");
	fclose($debugfile);
	***/
	
	//$url = str_replace("#wechat_redirect","", $url);
    //echo "<script>setTimeout(function(){window.location.href='".$url."';},5000);</script>";//将下一个将要跳转的$url变成js脚本，由anyproxy注入到微信页面中。
	//echo "<script>setTimeout(function(){window.location.href='".$url."'; window.event.returnValue=false;},2000);</script>";//将下一个将要跳转的$url变成js脚本，由anyproxy注入到微信页面中。
	echo "<meta http-equiv='refresh' content='2;url=$url' />";
	
	//<script>setTimeout(function(){window.location.href='http://mp.weixin.qq.com/s?__biz=MjM5MjAxNDM4MA==&mid=2666289986&idx=1&sn=ab40aa56d2d0e1e08680af3dd04612aa&chksm=bdb432018ac3bb17dd2825d9e928ed76c8041ae56d1d3d2659ac4081443ba66bc5af525566e0&scene=27#wechat_redirect';},2000);</script><!DOCTYPE html>
    //<script>setTimeout(function(){window.location.href='http://mp.weixin.qq.com/s?__biz=MjM5MjAxNDM4MA==&mid=2666289974&idx=1&sn=697c26f0eee5ad669d3617e47c0b96fb&chksm=bdb432758ac3bb63dbba2652b2ea055c743fb8cca24110bfeaecb97f746105c32cb7c0e64015&scene=27#wechat_redirect';},2000); window.event.returnValue=false; </script><!DOCTYPE html>

?>


