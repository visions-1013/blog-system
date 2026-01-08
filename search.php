<?php
session_start();
require_once 'config/db_connect.php';

// 获取搜索关键词
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// 查询搜索结果
$posts = [];
if (!empty($search_keyword)) {
    try {
        // 检查用户是否登录
        $is_logged_in = isset($_SESSION['user_id']);
        $current_user_id = $is_logged_in ? $_SESSION['user_id'] : 0;
        
        if ($is_logged_in) {
            // 已登录：查询点赞状态和头像
            $sql = "SELECT p.*, u.username, u.avatar,
                    CASE WHEN l.id IS NOT NULL THEN 1 ELSE 0 END as is_liked
                    FROM posts p 
                    LEFT JOIN users u ON p.user_id = u.id 
                    LEFT JOIN likes l ON p.id = l.post_id AND l.user_id = ?
                    WHERE p.content LIKE ? OR u.username LIKE ?
                    ORDER BY p.created_at DESC";
            $stmt = $pdo->prepare($sql);
            $keyword = "%$search_keyword%";
            $stmt->execute([$current_user_id, $keyword, $keyword]);
        } else {
            // 未登录：不查询点赞状态，但查询头像
            $sql = "SELECT p.*, u.username, u.avatar, 0 as is_liked
                    FROM posts p 
                    LEFT JOIN users u ON p.user_id = u.id 
                    WHERE p.content LIKE ? OR u.username LIKE ?
                    ORDER BY p.created_at DESC";
            $stmt = $pdo->prepare($sql);
            $keyword = "%$search_keyword%";
            $stmt->execute([$keyword, $keyword]);
        }
        $posts = $stmt->fetchAll();
    } catch (PDOException $e) {
        // 查询失败，$posts为空数组
        $posts = [];
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>搜索结果</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="static\css\search_style.css">
        <script src="static/js/ajax_req.js"></script>
        <script src="static/js/main.js"></script>
    </head>
    <body>
        <div class="container">
        <div class="header">
            <div class="head-left">
            <h3>搜索：<?php echo htmlspecialchars($search_keyword); ?> 的结果</h3>
            </div>
            <div class="head-right">
                <a href="index.php" class="index_button">返回主页</a>
            </div>
        </div>


        <div class="body">

            <div class="main-part">
            
            <?php if (empty($search_keyword)): ?>
                <div class="no-posts">
                    <p>请输入搜索关键词</p>
                </div>
            <?php elseif (empty($posts)): ?>
                <div class="no-posts">
                    <p>未找到包含"<?php echo htmlspecialchars($search_keyword); ?>"的相关微博</p>
                </div>
            <?php else: ?>
                <div class="search-info">
                    <p>找到 <?php echo count($posts); ?> 条相关微博</p>
                </div>
                
                <?php foreach ($posts as $post): ?>
                    <div class="blog" data-post-id="<?php echo $post['id']; ?>">
                        <div class="post-user">
                            <img src="static/img/<?php echo htmlspecialchars($post['avatar'] ?? 'default.png'); ?>" 
                                 class="post-user-avatar" 
                                 alt="<?php echo htmlspecialchars($post['username']); ?>的头像">
                            <div class="username"><?php echo htmlspecialchars($post['username']); ?></div>
                        </div>
                        <div class="blog-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
                        <?php if (!empty($post['image'])): ?>
                        <div class="blog-picture">
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="微博配图">
                        </div>
                        <?php endif; ?>
                        <button class="like-button" 
                                data-liked="<?php echo $post['is_liked'] ? 'true' : 'false'; ?>"
                                onclick="toggleLike(this)">
                            <span class="like-icon"></span>
                            <span><?php echo $post['is_liked'] ? '已赞' : '点赞'; ?></span>
                            <span class="like-count"><?php echo $post['likes_count']; ?></span>
                        </button>
                        <button class="comment-button" onclick="toggleComment(this)">
                            <span class="comment-icon">💬</span>
                            <span>评论</span>
                        </button>
                        <div class="comment-section" style="display: none;">
                            <!-- 评论将在这里动态加载 -->
                        </div>
                        <div class="comment-input-area" style="display: none;">
                            <textarea placeholder="写下你的评论..."></textarea>
                            <button class="comment-submit-btn" onclick="submitComment(this)">发表评论</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            </div>

            <div class="right-sider">
            <h4>热点内容</h4>
            <p>马杜罗的牺牲</p>
            <p>罗杰的死</p>
            <p>皇帝的雨伞</p>
            <p>小猫的重生</p>
            </div>
            
        </div>
        <div class="footer">
        <p>© 2026 XX微博 版权所有 | 开发者团队：219</p>
        <p>本页面为自制微博前端演示，后端功能待后续开发</p>
    </div>
        </body>
    </html>
