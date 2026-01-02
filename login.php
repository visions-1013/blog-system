<?php
session_start();
require_once __DIR__ . '/config/db_connect.php';

$serverMsg = '';
$serverMsgColor = 'darkred';
$oldUsername = '';

function validate_username(string $name): string {
    $name = trim($name);
    if ($name === '') return '用户名不能为空';
    if (mb_strlen($name) < 2 || mb_strlen($name) > 10) return '用户名长度需在2-10位之间';
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) return '用户名仅支持字母、数字和下划线';
    return '';
}

function validate_password(string $pass): string {
    $pass = trim($pass);
    if ($pass === '') return '密码不能为空';
    if (strlen($pass) < 6 || strlen($pass) > 16) return '密码长度需在6-16位之间';
    return '';
}

// 已登录就直接回主页
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
    $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
    $oldUsername = $username;

    $err = validate_username($username);
    if ($err === '') $err = validate_password($password);

    if ($err !== '') {
        $serverMsg = '无法登录：' . $err;
        $serverMsgColor = 'darkred';
    } else {
        try {
            $sql = "SELECT id, username, password, role, avatar FROM users WHERE username = ? LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user) {
                $serverMsg = '用户名或密码错误';
                $serverMsgColor = 'darkred';
            } else {
                $inputHash = md5($password);
                if (!hash_equals((string)$user['password'], $inputHash)) {
                    $serverMsg = '用户名或密码错误';
                    $serverMsgColor = 'darkred';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['username'] = (string)$user['username'];
                    $_SESSION['role'] = (int)$user['role'];
                    $_SESSION['avatar'] = (string)$user['avatar'];

                    header('Location: index.php');
                    exit;
                }
            }
        } catch (PDOException $e) {
            $serverMsg = '登录失败：数据库错误';
            $serverMsgColor = 'darkred';
        }
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>XX客户端--登录界面</title>
	</head>
	<body>
		<h3>请您输入您的用户信息!</h3>
		<form action="" method="post" onsubmit="return checkAll()">
			<p><b>用户昵称:</b>
                <!-- 失败后保留用户刚输入的 username -->
                <input type="text" name="username" id="username" placeholder="请输入您的用户名！" value="<?php echo htmlspecialchars($oldUsername, ENT_QUOTES, 'UTF-8'); ?>"/>
            </p>
			<p id="errInfo1" style="color:darkred">&nbsp&nbsp</p>
			<p><b>登录密码:</b><input type="password" name="password" id="password" placeholder="请输入6-16位密码，支持字母、数字和特殊字符！"/></p>
			<p id="errInfo2" style="color:darkred">&nbsp&nbsp</p>
			<p><input type="submit" name="submit" id="submit" value="现在登录!" />
			<input type="reset" name="reset" id="reset" value="重置信息!"></p>
            <p id="errInfo" style="color:darkred">
                <!--显示错误信息 -->
                <?php echo $serverMsg !== '' ? htmlspecialchars($serverMsg, ENT_QUOTES, 'UTF-8') : '&nbsp&nbsp'; ?>
            </p>
			</form>
	</body>
	<script>
		let username=document.getElementById("username");
		let password=document.getElementById("password");
		let resetBtn=document.getElementById("reset");
		let errInfo1=document.getElementById("errInfo1");
		let errInfo2=document.getElementById("errInfo2");
		let errInfo=document.getElementById("errInfo");
		username.addEventListener("blur",checkUsername);
		username.addEventListener("focus",checkUsername);
		username.addEventListener("input",checkUsername);
		password.addEventListener("blur",checkPassword);
		password.addEventListener("focus",checkPassword);
		password.addEventListener("input",checkPassword);
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
			        errInfo1.textContent="用户名由字母、数字和下划线组成!";
			        errInfo1.style.color = "darkred";
			        return false;
			    } else {
			        errInfo1.textContent="用户名格式正确!";
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
				errInfo2.textContent="密码格式正确!";
				errInfo2.style="color:green";
				return true;
			}
		}
		function checkAll(){
			let flag1=checkUsername();
			let flag2=checkPassword();
			if (flag1==false || flag2==false){
				errInfo.textContent="无法登录!内容缺失或者格式不正确!";
				errInfo.style="color:darkred";
				return false;
			}else{
				return true;
			}	
		}
		</script>
</html>