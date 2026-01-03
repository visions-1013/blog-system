<?php

require_once __DIR__ . '/config/db_connect.php';

$serverMsg = '';
$serverMsgColor = 'darkred';
$oldUsername = '';

//和前端逻辑差不多，就是重新检查一遍
function validate_username(string $name) {
    $name = trim($name);
    if ($name === '') return '用户名不能为空';
    if (mb_strlen($name) < 2 || mb_strlen($name) > 10) return '用户名长度需在2-10位之间';
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) return '用户名仅支持字母、数字和下划线';
    return '';
}
function validate_password(string $pass) {
    $pass = trim($pass);
    if ($pass === '') return '密码不能为空';
    if (strlen($pass) < 6 || strlen($pass) > 16) return '密码长度需在6-16位之间';
    return '';
}


function db_user_exists(string $username){
    global $pdo;

    $sql = "SELECT 1 FROM users WHERE username = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    return (bool)$stmt->fetchColumn();
}

function db_create_user(string $username, string $passwordPlain){
    global $pdo;
    $hash = md5($passwordPlain);

    $role = 0;
    $avatar = 'default.png';

    $sql = "INSERT INTO users (username, password, role, avatar) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        return $stmt->execute([$username, $hash, $role, $avatar]);
    } catch (PDOException $e) {
        return false;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
    $password   = isset($_POST['password']) ? (string)$_POST['password'] : '';
    $password_2 = isset($_POST['password_2']) ? (string)$_POST['password_2'] : '';
    $oldUsername = $username;

    $err = validate_username($username);
    if ($err === '') $err = validate_password($password);
    if ($err === '' && $password !== $password_2) $err = '两次输入的密码不一致';

    if ($err !== '') {
        $serverMsg = '无法注册：' . $err;
        $serverMsgColor = 'darkred';
    } else {
        if (db_user_exists($username)) {
            $serverMsg = '无法注册：该用户名已存在';
            $serverMsgColor = 'darkred';
        } else {
            if (db_create_user($username, $password)) {
                $serverMsg = '注册成功！';
                $serverMsgColor = 'green';
                
                //成功注册后，跳转登录页面
                header("Location: login.php"); 
                exit;
            } else {
                $serverMsg = '注册失败';
                $serverMsgColor = 'darkred';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>XX客户端注册界面</title>
	</head>
	<body>
		<h3>请输入您的用户信息!</h3>
		<form action="" method="post" onsubmit="return checkAll()">
			<p><b>用户昵称:</b><input type="text" name="username" id="username" placeholder="请输入您的用户名！" value="<?php echo htmlspecialchars($oldUsername, ENT_QUOTES, 'UTF-8'); ?>"/></p>
			<p id="errInfo1" style="color:darkred">&nbsp&nbsp</p>
			<p><b>登录密码:</b><input type="password" name="password" id="password" placeholder="请输入6-16位密码，支持字母、数字和特殊字符！"/></p>
			<p id="errInfo2" style="color:darkred">&nbsp&nbsp</p>
			<p><b>再次输入密码:</b><input type="password" name="password_2" id="password_2" placeholder="请再次输入您的登录密码！"/></p>
			<p id="errInfo3" style="color:darkred">&nbsp&nbsp</p>
			<p><input type="submit" name="submit" id="submit" value="现在注册!" />
			<input type="reset" name="reset" id="reset" value="重置信息!"></p>
			<p id="errInfo" style="color:darkred">
                <?php echo $serverMsg !== '' ? htmlspecialchars($serverMsg, ENT_QUOTES, 'UTF-8') : '&nbsp&nbsp'; ?>
            </p>
		</form>
	</body>
	<script>
	let username=document.getElementById("username");
	let password=document.getElementById("password");
	let password_2=document.getElementById("password_2");
	let resetBtn=document.getElementById("reset");
	let errInfo1=document.getElementById("errInfo1");
	let errInfo2=document.getElementById("errInfo2");
	let errInfo3=document.getElementById("errInfo3");
	let errInfo=document.getElementById("errInfo");
	username.addEventListener("blur",checkUsername);
	username.addEventListener("focus",checkUsername);
	username.addEventListener("input",checkUsername);
	password.addEventListener("blur",checkPassword);
	password.addEventListener("focus",checkPassword);
	password.addEventListener("input",checkPassword);
	password_2.addEventListener("blur",checkPassword_2);
	password_2.addEventListener("focus",checkPassword_2);
	password_2.addEventListener("input",checkPassword_2);
	resetBtn.addEventListener("click",resetAll);
	function checkUsername(){
		let name=username.value.trim();
		if (name.length==0){
		        errInfo1.textContent="请输入内容!";
		        errInfo1.style.color = "darkred";
		        return false;
		    } else if (name.length < 2 || name.length > 10) { 
		        errInfo1.textContent="用户名长度需在2-10位之间!";
		        errInfo1.style.color = "darkred";
		        return false;
		    } else if (!/^[a-zA-Z0-9_]+$/.test(name)) { 
		        errInfo1.textContent="用户名仅支持字母、数字和下划线!";
		        errInfo1.style.color = "darkred";
		        return false;
		    } else {
		        errInfo1.textContent="输入的内容符合标准!";
		        errInfo1.style="color:green";
		        return true;
		    }
	}
	function checkPassword(){
		let pass=password.value.trim();
		if (pass.length==0){
			errInfo2.textContent="请输入内容!";
			errInfo2.style="color:darkred";
			return false;
		}else if(pass.length<6 || pass.length>16){
			errInfo2.textContent="密码长度不符合标准!";
			errInfo2.style="color:darkred";
			return false;
			
		}else{
			errInfo2.textContent="输入的内容符合标准!";
			errInfo2.style="color:green";
			return true;
		}
	}
	function checkPassword_2(){
		let pass2=password_2.value.trim();
		let pass=password.value.trim();
		if (checkPassword()==false){
			errInfo3.textContent="输入的原始的登录密码不符合标准!";
			errInfo3.style="color:darkred";
			return false;
		}else if (pass2.length==0){
			errInfo3.textContent="请输入内容!";
			errInfo3.style="color:darkred";
			return false;
		}else if (pass2!=pass){
			errInfo3.textContent="再次输入的密码与第一次输入的密码不同!";
			errInfo3.style="color:darkred";
			return false;
		}else{
			errInfo3.textContent="输入的内容符合标准!";
			errInfo3.style="color:green";
			return true;
		}
	}
	function checkAll(){
		let flag1=checkUsername();
		let flag2=checkPassword();
		let flag3=checkPassword_2();
		if (flag1==false || flag2==false || flag3==false){
			errInfo.textContent="无法注册!内容填写有误!";
			errInfo.style="color:darkred";
			return false;
		}else{
			errInfo.textContent="注册成功!";
			errInfo.style="color:green";
			return true;
		}	
	}
	function resetAll(){
		errInfo.textContent="";
		errInfo1.textContent="";
		errInfo2.textContent="";
		errInfo3.textContent="";
		errInfo.style="color:darkred";
		errInfo1.style="color:darkred";
		errInfo2.style="color:darkred";
		errInfo3.style="color:darkred";
	}
	</script>
</html>