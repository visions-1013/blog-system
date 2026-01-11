<?php
session_start();
require_once __DIR__ . '/config/db_connect.php';

// 处理退出登录
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// 处理删除帖子
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $post_id = (int)$_POST['delete_post_id'];
    try {
        $sql = "DELETE FROM posts WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id]);
        // 删除成功，刷新页面
        header('Location: admin.php');
        exit;
    } catch (PDOException $e) {
        // 删除失败，可以添加错误处理
    }
}

// 处理删除用户
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $user_id = (int)$_POST['delete_user_id'];
    try {
        // 先删除该用户的所有帖子
        $sql = "DELETE FROM posts WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        
        // 再删除用户
        $sql = "DELETE FROM users WHERE id = ? AND role != 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        
        // 删除成功，刷新页面
        header('Location: admin.php');
        exit;
    } catch (PDOException $e) {
        // 删除失败，可以添加错误处理
    }
}

// 检查是否已登录
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// 检查是否为管理员
if ((int)$_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit;
}

$adminUsername = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') : '管理员';

// 判断当前激活的标签
$activeTab = 'content'; // 默认内容管理

// 处理用户搜索关键词
$userSearchKeyword = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_search'])) {
    $userSearchKeyword = trim($_GET['user_search']);
    // 如果有用户搜索参数，激活用户管理标签
    $activeTab = 'user';
}

// 处理帖子搜索关键词
$searchKeyword = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
}

// 查询帖子（带搜索功能）
$posts = [];
try {
    if (!empty($searchKeyword)) {
        // 带搜索条件的查询
        $sql = "SELECT p.*, u.username 
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.content LIKE ? OR u.username LIKE ?
                ORDER BY p.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $searchTerm = "%{$searchKeyword}%";
        $stmt->execute([$searchTerm, $searchTerm]);
    } else {
        // 查询所有帖子
        $sql = "SELECT p.*, u.username 
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC";
        $stmt = $pdo->query($sql);
    }
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    // 查询失败，$posts为空数组
    $posts = [];
}

// 查询用户（带搜索功能）
$users = [];
try {
    if (!empty($userSearchKeyword)) {
        // 带搜索条件的查询（按用户名搜索）
        $sql = "SELECT id, username, role, created_at 
                FROM users 
                WHERE username LIKE ?
                ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $searchTerm = "%{$userSearchKeyword}%";
        $stmt->execute([$searchTerm]);
    } else {
        // 查询所有用户
        $sql = "SELECT id, username, role, created_at 
                FROM users 
                ORDER BY id DESC";
        $stmt = $pdo->query($sql);
    }
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    // 查询失败，$users为空数组
    $users = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>XX微博 - 管理后台</title>
    <link rel="stylesheet" href="static/css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="static/js/ajax_req.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="head-left">
                <h3><i class="fa-solid fa-user-shield"></i> 管理员 <?php echo $adminUsername; ?>，您好！</h3>
            </div>
            <div class="head-right">
                <h3><i class="fa-solid fa-gear"></i> 管理系统</h3>
                <form action="" method="post">
                    <button type="submit" name="logout" value="1" class="admin-btn btn-warning">
                        <i class="fa-solid fa-right-from-bracket"></i> 退出登录
                    </button>
                </form>
            </div>
        </div>

        <div class="admin-nav">
            <ul>
                <li class="<?php echo $activeTab === 'content' ? 'active' : ''; ?>" onclick="switchTab('content')"><a href="javascript:;"><i class="fa-solid fa-file-lines"></i> 内容管理</a></li>
                <li class="<?php echo $activeTab === 'user' ? 'active' : ''; ?>" onclick="switchTab('user')"><a href="javascript:;"><i class="fa-solid fa-users"></i> 用户管理</a></li>
            </ul>
        </div>

        <div class="body">
            <div class="main-part">
                <!-- 内容管理部分 -->
                <div id="content-management" class="admin-display" style="display: <?php echo $activeTab === 'content' ? 'block' : 'none'; ?>;">
                    <h4>发帖内容管理</h4>
                    
                    <!-- 搜索区域 -->
                    <div class="search-area">
                        <form action="" method="get" class="search-form">
                            <div class="search-wrapper">
                                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                <input type="text" class="search-input" name="search" 
                                       placeholder="搜索用户名或内容..."
                                       value="<?php echo htmlspecialchars($searchKeyword); ?>">
                            </div>
                            <button type="submit" class="admin-btn"><i class="fa-solid fa-search"></i> 搜索</button>
                            <?php if (!empty($searchKeyword)): ?>
                                <a href="admin.php" class="admin-btn btn-clear">
                                    <i class="fa-solid fa-xmark"></i> 清除
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <?php if (!empty($searchKeyword)): ?>
                        <div class="search-result-info">
                            搜索结果（关键词："<?php echo htmlspecialchars($searchKeyword); ?>"）：共找到 <?php echo count($posts); ?> 条记录
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($posts)): ?>
                        <div class="data-card" style="text-align: center; padding: 20px;">
                            <p style="color: #718096;">暂无帖子</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <!-- 用户发帖内容 -->
                            <div class="data-card">
                                <div class="post-info">
                                    <div>
                                        <strong>用户：</strong><?php echo htmlspecialchars($post['username']); ?> (ID: <?php echo $post['id']; ?>) |
                                        <strong>时间：</strong><?php echo date('Y-m-d H:i', strtotime($post['created_at'])); ?>
                                    </div>
                                </div>
                                <div class="post-content">
                                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                                </div>
                                <?php if (!empty($post['image'])): ?>
                                    <div class="post-image">
                                        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="微博配图">
                                    </div>
                                <?php endif; ?>
                                <div style="margin-top: 10px;">
                                    <button class="admin-btn" onclick="toggleAdminComments(<?php echo $post['id']; ?>)">
                                        <i class="fa-solid fa-comments"></i> 查看评论
                                    </button>
                                    <div id="admin-comments-<?php echo $post['id']; ?>" class="admin-comment-section" style="display: none;"></div>
                                    <form action="" method="post" style="display: inline-block; margin-left: 10px;">
                                        <input type="hidden" name="delete_post_id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" class="admin-btn btn-danger" 
                                                onclick="return confirmDeletePost(event, '<?php echo $post['id']; ?>', '<?php echo htmlspecialchars($post['username']); ?>')">
                                            删除帖子
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 用户管理部分 -->
                <div id="user-management" class="admin-display" style="display: <?php echo $activeTab === 'user' ? 'block' : 'none'; ?>;">
                    <h4>用户管理</h4>
                    
                    <!-- 用户搜索 -->
                    <div class="search-area">
                        <form action="" method="get" class="search-form">
                            <div class="search-wrapper">
                                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                <input type="text" class="search-input" name="user_search" 
                                       placeholder="搜索用户名..."
                                       value="<?php echo htmlspecialchars($userSearchKeyword); ?>">
                            </div>
                            <button type="submit" class="admin-btn"><i class="fa-solid fa-search"></i> 搜索</button>
                            <?php if (!empty($userSearchKeyword)): ?>
                                <a href="admin.php" class="admin-btn btn-clear">
                                    <i class="fa-solid fa-xmark"></i> 清除
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <?php if (!empty($userSearchKeyword)): ?>
                        <div class="search-result-info">
                            搜索结果（关键词："<?php echo htmlspecialchars($userSearchKeyword); ?>"）：共找到 <?php echo count($users); ?> 个用户
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($users)): ?>
                        <div class="data-card" style="text-align: center; padding: 20px;">
                            <p style="color: #718096;">暂无用户</p>
                        </div>
                    <?php else: ?>
                        <div class="user-list">
                            <?php foreach ($users as $user): ?>
                                <div class="user-card">
                                    <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                                    <div class="user-id">ID: <?php echo $user['id']; ?></div>
                                    <div class="user-status <?php echo (int)$user['role'] === 1 ? 'status-admin' : 'status-normal'; ?>">
                                        <?php echo (int)$user['role'] === 1 ? '管理员' : '正常用户'; ?>
                                    </div>
                                    <div>
                                        <?php if ((int)$user['role'] !== 1): ?>
                                        <form action="" method="post">
                                            <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="admin-btn btn-danger" style="font-size: 11px; padding: 4px 8px;"
                                                    onclick="return confirmDeleteUser(event, '<?php echo $user['id']; ?>', '<?php echo htmlspecialchars($user['username']); ?>')">
                                                删除用户
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <h4 style="margin-top: 20px;">用户统计</h4>
                        <div class="user-statistics">
                            <div>总用户数: <?php echo count($users); ?></div>
                            <?php 
                            $adminCount = 0;
                            foreach ($users as $user) {
                                if ((int)$user['role'] === 1) $adminCount++;
                            }
                            ?>
                            <div>管理员: <?php echo $adminCount; ?></div>
                            <div>普通用户: <?php echo count($users) - $adminCount; ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        © 2026 XX微博 版权所有 | 管理系统<br/>
            开发者团队：周楷涵、章晨阳、章航渝
    </div>

    <script>
        // 删除帖子确认
        function confirmDeletePost(event, postId, username) {
            event.preventDefault(); // 阻止表单默认提交
            
            // 获取微博内容
            const postContent = event.target.closest('.data-card').querySelector('.post-content').textContent.trim();
            
            // 显示确认对话框（最多显示50个字符，超出显示"..."）
            if (confirm('确认删除这条微博吗？\n\n发布者：' + username + '\n\n内容：' + postContent.substring(0, 50) + (postContent.length > 50 ? '...' : ''))) {
                // 用户确认，提交表单
                event.target.closest('form').submit();
            } else {
                // 用户取消，阻止表单提交
                return false;
            }
        }
    
        // 删除用户确认
        function confirmDeleteUser(event, userId, username) {
            event.preventDefault(); // 阻止表单默认提交
            
            // 显示确认对话框
            if (confirm('确认删除用户：' + username + '\n\n删除用户将同时删除其所有发布的微博！\n\n此操作不可恢复！')) {
                // 用户确认，提交表单
                event.target.closest('form').submit();
            } else {
                // 用户取消，阻止表单提交
                return false;
            }
        }
        
        // 切换标签页
        function switchTab(tabName) {
            // 获取所有管理区域的元素
            var contentManagement = document.getElementById('content-management');
            var userManagement = document.getElementById('user-management');
            
            // 获取所有导航项
            var navItems = document.querySelectorAll('.admin-nav li');
            
            // 移除所有导航项的active类
            navItems.forEach(function(item) {
                item.classList.remove('active');
            });
            
            // 根据点击的标签显示对应内容
            if (tabName === 'content') {
                contentManagement.style.display = 'block';
                userManagement.style.display = 'none';
                navItems[0].classList.add('active');
            } else if (tabName === 'user') {
                contentManagement.style.display = 'none';
                userManagement.style.display = 'block';
                navItems[1].classList.add('active');
            }
        }
        
        // 管理员查看/隐藏评论
        function toggleAdminComments(postId) {
            var commentSection = document.getElementById('admin-comments-' + postId);
            
            if (commentSection.style.display === 'none' || commentSection.style.display === '') {
                commentSection.style.display = 'block';
                loadAdminComments(postId, commentSection);
            } else {
                commentSection.style.display = 'none';
            }
        }
        
        // 加载管理员评论列表
        function loadAdminComments(postId, commentSection) {
            commentSection.innerHTML = '<p style="text-align:center;color:#999;">加载中...</p>';
            
            ajaxRequest('GET', 'api/action_get_comments.php', {
                post_id: postId
            }, function(error, response) {
                if (error) {
                    commentSection.innerHTML = '<p style="text-align:center;color:red;">加载失败，请重试</p>';
                    return;
                }
                
                if (!response.success) {
                    commentSection.innerHTML = '<p style="text-align:center;color:red;">' + escapeHtml(response.error) + '</p>';
                    return;
                }
                
                // 显示评论列表
                if (response.comments.length === 0) {
                    commentSection.innerHTML = '<p style="text-align:center;color:#999;">暂无评论</p>';
                } else {
                    commentSection.innerHTML = '<h5 style="margin-bottom:12px;color:#666;">评论列表 (' + response.comments.length + ')</h5>';
                    response.comments.forEach(comment => {
                        createAdminCommentElement(comment, commentSection);
                    });
                }
            });
        }
        
        // 创建管理员评论DOM元素
        function createAdminCommentElement(comment, commentSection) {
            var commentDiv = document.createElement('div');
            commentDiv.className = 'comment-item';
            commentDiv.setAttribute('data-comment-id', comment.id);
            commentDiv.style.marginBottom = '12px';
            commentDiv.style.padding = '12px';
            commentDiv.style.border = '1px solid #e2e8f0';
            commentDiv.style.borderRadius = '8px';
            commentDiv.style.backgroundColor = '#f8fafc';
            
            var deleteButtonHtml = '';
            if (comment.can_delete) {
                deleteButtonHtml = `
                    <button class="delete-comment-btn" onclick="confirmDeleteComment(this)" title="删除评论">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                `;
            }
            
            commentDiv.innerHTML = `
                <div class="comment-content-wrapper">
                    <strong>${escapeHtml(comment.username)}</strong>
                    <small style="color:#666;margin-left:8px;">${comment.created_at}</small>
                    <div style="margin-top:8px;color:#333;">${escapeHtml(comment.content)}</div>
                    ${deleteButtonHtml}
                </div>
            `;
            
            commentSection.appendChild(commentDiv);
        }
        
        // HTML转义
        function escapeHtml(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // 确认删除评论
        function confirmDeleteComment(button) {
            const commentDiv = button.closest('.comment-item');
            const commentContent = commentDiv.querySelector('.comment-content-wrapper div:last-of-type').textContent.trim();
            
            // 二次确认
            if (confirm('确定要删除这条评论吗？')) {
                const commentId = commentDiv.getAttribute('data-comment-id');
                deleteAdminComment(button, commentId);
            }
        }
        
        // 执行删除评论
        function deleteAdminComment(button, commentId) {
            const commentDiv = button.closest('.comment-item');
            
            button.disabled = true;
            button.style.opacity = '0.5';
            
            ajaxRequest('POST', 'api/action_delete_comment.php', {
                comment_id: commentId
            }, function(error, response) {
                if (error) {
                    alert(error);
                    button.disabled = false;
                    button.style.opacity = '1';
                    return;
                }
                
                if (!response.success) {
                    alert(response.error);
                    button.disabled = false;
                    button.style.opacity = '1';
                    return;
                }
                
                // 删除成功，移除评论元素
                commentDiv.style.transition = 'opacity 0.3s ease';
                commentDiv.style.opacity = '0';
                
                setTimeout(() => {
                    commentDiv.remove();
                    
                    // 检查是否还有评论，如果没有则更新评论数量
                    const commentSection = commentDiv.parentElement;
                    const remainingComments = commentSection.querySelectorAll('.comment-item');
                    const countHeader = commentSection.querySelector('h5');
                    if (countHeader) {
                        countHeader.textContent = '评论列表 (' + remainingComments.length + ')';
                    }
                    
                    if (remainingComments.length === 0) {
                        commentSection.innerHTML = '<p style="text-align:center;color:#999;">暂无评论</p>';
                    }
                    
                    alert('删除成功！');
                }, 300);
            });
        }
    </script>
</body>
</html>
