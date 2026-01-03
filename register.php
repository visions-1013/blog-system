<?php

require_once 'config/db_connect.php';

$serverMsg = '';
$serverMsgColor = 'darkred';
$oldUsername = '';

//和前端逻辑差不多，就是重新检查一遍
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

//
/* ========= 数据库占位，暂时默认成功 ========= */
function db_user_exists(string $username): bool {
    return false; // 默认不存在
}
function db_create_user(string $username, string $passwordPlain): bool {
    return true; // 默认成功
}
/* ==================================================== */
//


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
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XX微博 - 注册</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- 标题区域 -->
    <div class="auth-title">
        <h1>创建XX微博账号</h1>
        <p>填写以下信息，开启您的社交之旅</p>
    </div>

    <!-- 注册表单 -->
    <div class="auth-form">
        <form action="" method="post" onsubmit="return checkAll()">
            <div class="form-group">
                <label for="username">用户昵称：</label>
                <input type="text" name="username" id="username" 
                       placeholder="请输入您的用户名！" 
                       value="<?php echo htmlspecialchars($oldUsername, ENT_QUOTES, 'UTF-8'); ?>"/>
                <p id="errInfo1" class="error-message">&nbsp;</p>
            </div>

            <div class="form-group">
                <label for="password">登录密码：</label>
                <input type="password" name="password" id="password" 
                       placeholder="请输入6-16位密码，支持字母、数字和特殊字符！"/>
                <p id="errInfo2" class="error-message">&nbsp;</p>
            </div>

            <div class="form-group">
                <label for="password_2">再次输入密码：</label>
                <input type="password" name="password_2" id="password_2" 
                       placeholder="请再次输入您的登录密码！"/>
                <p id="errInfo3" class="error-message">&nbsp;</p>
            </div>

            <div class="form-group">
                <p id="serverMsg" class="error-message" style="color:<?php echo $serverMsgColor; ?>">
                    <?php echo $serverMsg !== '' ? htmlspecialchars($serverMsg, ENT_QUOTES, 'UTF-8') : '&nbsp;'; ?>
                </p>
            </div>

            <div class="btn-group">
                <input type="submit" name="submit" id="submit" value="现在注册!" class="btn-primary" />
                <input type="reset" name="reset" id="reset" value="重置信息!" class="btn-secondary">
            </div>
        </form>
    </div>

    <!-- 底部版权 -->
    <div class="auth-footer">
        <p>© 2026 XX微博 版权所有 | 开发者团队：219</p>
        <p>本页面为自制微博前端演示，后端功能待后续开发</p>
    </div>
</body>
<script>
    <!-- 保留原有的JavaScript验证逻辑 -->
</script>
</html>