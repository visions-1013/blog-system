<?php
$db_host = 'localhost';     
$db_name = 'weibo_sys';      
$db_user = 'root';            
$db_pass = '';                
$db_charset = 'utf8mb4';     

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // 设置默认 fetch 模式为关联数组（['id'=>1, 'name'=>'张三']）
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
?>
