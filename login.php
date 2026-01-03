<?php
session_start();
require_once __DIR__ . '/config/db_connect.php';

$serverMsg = '';
$oldUsername = '';

// 用户名验证函数
function validate_username(string $name): string {
    $name = trim($name);
    if ($name === '') return '用户名不能为空';
    if (mb_strlen($name) < 2 || mb_strlen($name) > 10) return '用户名长度需在2-10位之间';
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) return '用户名仅支持字母、数字和下划线';
    return '';
}

// 密码验证函数
function validate_password(string $pass): string {
    $pass = trim($pass);
    if ($pass === '') return '密码不能为空';
    if (strlen($pass) < 6 || strlen($pass) > 16) return '密码长度需在6-16位之间';
    return '';
}

// 已登录用户直接跳转主页
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// 处理登录提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
    $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
    $oldUsername = $username;

    // 验证输入
    $err = validate_username($username);
    if ($err === '') $err = validate_password($password);

    if ($err !== '') {
        $serverMsg = '无法登录：' . $err;
    } else {
        try {
            // 查询用户
            $sql = "SELECT id, username, password, role, avatar FROM users WHERE username = ? LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user) {
                $serverMsg = '用户名或密码错误';
            } else {

                $inputHash = md5($password);
                if (!hash_equals((string)$user['password'], $inputHash)) {
                    $serverMsg = '用户名或密码错误';
                } else {
                    // 登录成功，更新会话
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
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XX微博 - 登录</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-title">
        <h1>欢迎回到XX微博</h1>
        <p>输入您的账号和密码，开启精彩社交</p>
    </div>

    <div class="auth-form login-form">
        <form action="" method="post">
            <div class="form-group">
                <label for="username">用户昵称：</label>
                <input type="text" id="username" name="username" 
                       placeholder="请输入您的用户名！" 
                       value="<?php echo htmlspecialchars($oldUsername, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="form-group">
                <label for="password">登录密码：</label>
                <input type="password" id="password" name="password" 
                       placeholder="请输入6-16位密码，支持字母、数字和特殊字符！">
            </div>

            <!-- 错误信息显示 -->
            <?php if ($serverMsg): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($serverMsg, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="btn-group">
                <button type="submit" class="btn-primary">现在登录！</button>
                <button type="reset" class="btn-secondary">重置信息！</button>
            </div>
        </form>
    </div>

    <div class="auth-footer">
        <p>© 2026 XX微博 版权所有 | 开发者团队：219</p>
        <p>本页面为自制微博前端演示，后端功能待后续开发</p>
    </div>
</body>
</html>