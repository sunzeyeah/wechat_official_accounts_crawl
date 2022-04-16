<?php

	$serve = 'localhost:3306';
	$username = 'weiting';
	$password = '12w34567';
	$dbname = 'weixin';
	$link = mysqli_connect($serve,$username,$password,$dbname);
	mysqli_set_charset($link,'UTF-8'); // 设置数据库字符集

	$str = $_POST['str'];
	$url = $_POST['url'];//先获取到两个POST变量
	//先针对url参数进行操作
	parse_str(parse_url(htmlspecialchars_decode(urldecode($url)),PHP_URL_QUERY ),$query);//解析url地址
	$biz = $query['__biz'];//得到公众号的biz
	$sn = $query['sn'];
	
	/***
	$debugfile = fopen("debug_getappmsgext.txt", "a+");
	fwrite($debugfile, json_encode($query) ."\n");
	fclose($debugfile);
	***/

	//再解析str变量
	//$json = json_decode($str,true);//进行json_decode
	$str = urldecode($str); // 对应js中的encodeURIComponent方法
	$json = json_decode($str,true);//首先进行json_decode
	if(!$json){
		$json = json_decode(htmlspecialchars_decode($str),true);//如果不成功，就增加一步htmlspecialchars_decode
	}
	
	$read_num = $json['appmsgstat']['read_num'];//阅读量
	$like_num = $json['appmsgstat']['like_num'];//点赞量
	
	//根据biz和sn找到对应的文章
	$sql = "select * from `post` where `biz`='$biz' and `sn` like '$sn' limit 0,1";
	$result = mysqli_query($link, $sql);
	//$data = mysqli_fetch_all($result); 
	$row = mysqli_fetch_assoc($result);  
	$id = $row['id'];
	
	/***
	$debugfile = fopen("debug_getappmsgext.txt", "a+");
	fwrite($debugfile, $sql ."\n");
	fclose($debugfile);
	***/
	
	//在这里同样根据sn在采集队列表中删除对应的文章，代表这篇文章可以移出采集队列了
	//$sql = "delete from `队列表` where `content_url` like '%".$sn."%'" 
				
	//然后将阅读量和点赞量更新到文章表中。
	$sql = "update post set readNum=$read_num, likeNum=$like_num where id=$id";
	$result = mysqli_query($link, $sql);
	
	/***
	$debugfile = fopen("debug_getappmsgext.txt", "a+");
	fwrite($debugfile, $sql ."\n");
	fclose($debugfile);***/
	
	
	exit(json_encode($query));//可以显示在anyproxy的终端里
	
?>