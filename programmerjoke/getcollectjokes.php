<?php
require_once('function.php');

$userId = $_GET['userId'];
$page = $_GET['page'];
$limit = $_GET['limit'];

if (!isset($page))
{
	$page = 1;
}
if (!isset($limit))
{
	$limit = 20;
}

$offset = ($page -1) * $limit;

function getCollectJoke($con, $userId, $offset, $limit)
{
	$sql = "SELECT Q2.joke_id, Q2.content, Q2.create_time, Q2.user_id, Q2.images_url, Q2.islike, Q2.collect_count, Q2.like_count, pj_user.avatar, pj_user.nick_name FROM 
(

	SELECT Q1.joke_id, Q1.content, Q1.create_time, Q1.user_id, Q1.images_url, Q1.islike, IFNULL(P2.collect_count, 0) AS collect_count, IFNULL(P2.like_count, 0) AS like_count FROM 
	(
		SELECT pj_jokes.joke_id, pj_jokes.content, pj_jokes.create_time, pj_jokes.user_id, pj_jokes.images_url, IFNULL(pj_collect_and_like.islike, 0) AS islike FROM pj_collect_and_like LEFT JOIN pj_jokes ON 
		pj_collect_and_like.joke_id = pj_jokes.joke_id 
		WHERE pj_collect_and_like.user_id = '$userId' AND pj_collect_and_like.iscollect = 1 
		ORDER BY pj_collect_and_like.id DESC LIMIT $offset, $limit
	) AS Q1 

	LEFT JOIN 
	(	SELECT joke_id, COUNT(iscollect) AS collect_count, COUNT(islike) AS like_count  
		FROM pj_collect_and_like 
		GROUP BY joke_id
	) AS P2 

	ON Q1.joke_id = P2.joke_id

) AS Q2



LEFT JOIN pj_user ON Q2.user_id = pj_user.user_id";
	
	return mysql_query($sql, $con);
	
}

$con = connectDB();

$result = getCollectJoke($con, $userId, $offset, $limit);
$jokesList = array();

$i = 0;
while($row = mysql_fetch_array($result))
{
	$jokeId = iconv("GBK", "UTF-8", $row['joke_id']);
	$jokeContent = iconv("GBK", "UTF-8", $row['content']);
	$createTime = iconv("GBK", "UTF-8", $row['create_time']);
	$userId = iconv("GBK", "UTF-8", $row['user_id']);
	$nickName = iconv("GBK", "UTF-8", $row['nick_name']);
	$avatar = iconv("GBK", "UTF-8", $row['avatar']);
	$islike = $row['islike'];
	$likeCount = $row['like_count'];
	$collectCount = $row['collect_count'];
	$imagesUrl = $row['images_url'];

	$jokesArr = array('jokeId' => $jokeId,
						'jokeContent' => $jokeContent,
						'createTime' => $createTime,
						'userId' => $userId,
						'nickName' => $nickName,
						'avatar' => $avatar,
						'islike' => $islike,
						'likeCount' => $likeCount,
						'collectCount' => $collectCount,
						'imagesUrl' => $imagesUrl);
	$jokesList[$i++] = $jokesArr;
}
$totalCount = mysql_num_rows($result);

echo json_encode(array('status' => '1', 'totalCount' => $totalCount, 'jokesList' => $jokesList, 'function' => 'getcollectjokes.php'));

mysql_close($con);
mysql_free_result($result);

?>
