<?php
require_once('function.php');

// 用户登录
function login($con, $accountNumber, $password)
{

    // 检查电邮地址语法是否有效
    if (preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $accountNumber)) {
		$sql = "SELECT * FROM pj_user WHERE email = '$accountNumber'";
    } else {
		$sql = "SELECT * FROM pj_user WHERE phone_num = '$accountNumber'";
	}
	
	$res = mysql_query($sql, $con);

	if($row = mysql_fetch_array($res))
	{

		if(0 == strcmp($password, $row['password']))
		{
			$userId = $row['user_id'];
			$nickName = iconv("GBK", "UTF-8", $row['nick_name']);
			$sex = $row['sex'];
			$age = $row['age'];
			$avatar = $row['avatar'] . "?imageView2/2/w/200/h/200/q/100/format/JPG";
			$city = iconv("GBK", "UTF-8", $row['city']);
			$registerTime = iconv("GBK", "UTF-8", $row['register_time']);
			$loginTime = iconv("GBK", "UTF-8", $row['login_time']);
			$loginType = iconv("GBK", "UTF-8", $row['login_type']);

			$userInfo = array('userId' => $userId, 
					'nickName' => $nickName,
					'sex' => $sex,
					'age' => $age,
					'avatar' => $avatar,
					'city' => $city,
					'registerTime' => $registerTime,
					'loginTime' => $loginTime,
					'loginType' => $loginType);

			echo json_encode(array('status' => '1', 'userInfo' => $userInfo, 'function' => 'login.php'));
		}
		else
		{
			echo json_encode(array('status' => '2', 'error' => 'password error', 'function' => 'login.php'));
		}
	}
	else
	{
		echo json_encode(array('status' => '3', 'error' => 'user non-existent', 'function' => 'login.php'));
	}

	mysql_free_result($res);
}

$accountNumber = $_POST["accountNumber"];
$password = $_POST["password"];

if (isset($accountNumber) && !empty($accountNumber) && isset($password) && !empty($password))
{
	$con = connectDB();

	login($con, $accountNumber, $password);

	mysql_close($con);
}
else
{
	echo json_encode(array('status' => '4', 'error' => 'accountNumber or password null', 'function' => 'login.php'));
}
	
?>
