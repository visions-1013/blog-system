<?php
session_start();
require_once 'config/db_connect.php';

// 检查用户登录状态
$is_logged_in = isset($_SESSION['user_id']);
$current_user = null;
if ($is_logged_in) {
    $current_user = $_SESSION['user_id'];
}
// 处理登出请求
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // 销毁所有session变量
    $_SESSION = array();
    
    // 删除session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    
    // 销毁session
    session_destroy();
    
    // 重定向到登录页面
    header('Location: login.php');
    exit;
}

// 处理微博发布
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content-submit'])) {
    // 验证用户登录
    if (!$is_logged_in) {
        echo "<script>alert('请先登录！'); window.location.href='login.php';</script>";
        exit;
    }
    
    // 获取微博内容
    $content = trim($_POST['contentInput']);
    
    // 验证内容
    if (empty($content)) {
        echo "<script>alert('微博内容不能为空！'); history.back();</script>";
        exit;
    }
    
    // 处理图片上传
    $image_path = null;
    if (isset($_FILES['weibo-picture']) && $_FILES['weibo-picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'static/img/';
        
        // 检查目录是否存在，不存在则创建
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file = $_FILES['weibo-picture'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // 验证文件类型
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_types)) {
            echo "<script>alert('只允许上传 JPG、JPEG、PNG、GIF 格式的图片！'); history.back();</script>";
            exit;
        }
        
        // 验证文件大小（限制为5MB）
        if ($file['size'] > 5 * 1024 * 1024) {
            echo "<script>alert('图片大小不能超过5MB！'); history.back();</script>";
            exit;
        }
        
        // 生成唯一文件名
        $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;
        
        // 移动上传文件
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $image_path = $upload_path;
        } else {
            echo "<script>alert('图片上传失败，请重试！'); history.back();</script>";
            exit;
        }
    }
    
    // 插入数据库
    try {
        $sql = "INSERT INTO posts (user_id, content, image, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$current_user, $content, $image_path]);
        
        // 发布成功，直接刷新页面，不显示提示
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('发布失败：" . $e->getMessage() . "'); history.back();</script>";
        exit;
    }
}



// 查询微博列表
$posts = [];
try {
    $sql = "SELECT p.*, u.username 
            FROM posts p 
            LEFT JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC";
    $stmt = $pdo->query($sql);
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    // 查询失败，$posts为空数组
    $posts = [];
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>XX微博主页面</title>
		<link rel="stylesheet" href="static\css\index_style.css">
	</head>
	<body>
		<div class="container">
			<div class="header">
				<div class="head-left">
					<h3>欢迎您访问XX微博!!在此记录生活，畅抒己见!</h3></br>
					<form method="get" action="search.php">
						<input type="text" name="search" id="search" placeholder="搜索您想看的blog..."/>
						<input type="submit" name="search-submit" id="search-submit" value="点击搜索"/>
					</form>
				</div>
				<div class="head-right">
					<?php if ($is_logged_in): ?>
						<!-- 已登录状态 -->
						<div class="user-info">
							<span class="username-display"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
						</div>
						
						<!-- 用户头像显示 -->
						<div class="user-avatar-container">
							<form action="profile.php" method="post" enctype="multipart/form-data">
								<input type="file" name="avatar" id="avatarUpload" 
								       accept="image/*" style="display:none;" onchange="this.form.submit()"/>
								<img src="static/img/<?php echo htmlspecialchars($_SESSION['avatar'] ?? 'default.png'); ?>" 
								     alt="用户头像" 
								     class="user-avatar" 
								     onclick="document.getElementById('avatarUpload').click()"/>
							</form>
						</div>
						<nav class="user-nav">
							<a href="profile.php">个人中心</a>
							<a href="?action=logout">退出</a>
						</nav>

					<?php else: ?>
						<!-- 未登录状态 -->
						<nav class="auth-nav">
							<a href="login.php">登录</a>
							<span>|</span>
							<a href="register.php">注册</a>
						</nav>
					<?php endif; ?>
				</div>
			</div>
			<div class="nav">
				今日新闻&nbsp;&nbsp;&nbsp;&nbsp;明星趣闻&nbsp;&nbsp;&nbsp;&nbsp;学习妙招
			</div>
			<div class="body">
				<div class="left-sider">
					<p>个人中心</p>
					<p>特别关注</p>
					<p>您的好友</p>
					<p>关于我们(开发者团队)</p>
				</div>
				<div class="main-part">
				    <div class="blog-area">
						<form method="post" action="" enctype="multipart/form-data">
							<textarea placeholder="分享您的新鲜事···" rows="5" cols="30"
							name="contentInput" id="contentInput"></textarea>
							<br/>
							<div class="errInfo" id="errInfo">&nbsp;&nbsp;</div>
							<!-- 优化后的文件上传组件 -->
							<div class="file-upload-container">
								<label for="weibo-picture" class="file-upload-button">
									<span class="file-upload-icon"></span>
									选择图片
								</label>
								<input type="file" name="weibo-picture" id="weibo-picture"
								accept="image/jpeg,image/png,image/gif" style="display:none;" onchange="updateFileName(this)"/>
							</div>
							<span id="file-name" class="file-name-display">未选择文件</span>
							&nbsp;
							<input type="submit" name="content-submit" id="content-submit" value="一键分享">
						</form>
					</div><br/><br/>
					<div class="blog-display">
						<?php if (empty($posts)): ?>
							<div class="no-posts">
								<p>暂无微博，快来发布第一条吧！</p>
							</div>
						<?php else: ?>
							<?php foreach ($posts as $post): ?>
								<div class="blog">
									<div class="username"><?php echo htmlspecialchars($post['username']); ?></div>
									<div class="blog-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
									<?php if (!empty($post['image'])): ?>
									<div class="blog-picture">
										<img src="<?php echo htmlspecialchars($post['image']); ?>" alt="微博配图">
									</div>
									<?php endif; ?>
									<div class="post-time">
										<small><?php echo date('Y-m-d H:i:s', strtotime($post['created_at'])); ?></small>
									</div>
									<button class="like-button" onclick="toggleLike(this)">
										<span class="like-icon"></span>
										<span>点赞</span>
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
				</div>
				<div class="right-sider">
					<p>热点新闻</p>
					<div class="hot-new">1.震惊！我们杭州又赢了！！</div>
					<div class="hot-new">2.小猪佩奇居然是我们杭州人？就在刚刚·······</div>
					<div class="hot-new">3.一杭州小伙研制出了利器，重大突破！</div>
					<div class="hot-new">4.中国5：德国0 好消息传来----</div>
					<div >
				</div>
			</div>
		</div>
		<div class="footer">
			© 2026 XX微博 版权所有 | 隐私政策 | 联系我们<br/>
			    开发者团队：章航渝、章晨阳、周凯涵<br/>
			    本页面为自制微博前端演示，后端功能待后续开发
		</div>
		
		<script>
			let contentInput = document.getElementById('contentInput');
			contentInput.addEventListener('input', function() {
				let content = contentInput.value;
				let errInfo = document.getElementById('errInfo');
				
				if (content.length > 140) {
					errInfo.textContent = '字数超出限制！请精简您的内容。';
					errInfo.style.color = 'red';
				} else {
					errInfo.textContent = '';
				}
			})
			// 点赞功能实现
			function toggleLike(button) {
				const likeCountSpan = button.querySelector('.like-count');
				let likeCount = parseInt(likeCountSpan.textContent);
				
				// 添加动画类
				button.classList.add('animating');
				
				// 切换点赞状态
				if (button.classList.contains('liked')) {
					// 取消点赞
					button.classList.remove('liked');
					likeCount--;
					button.querySelector('span:nth-child(2)').textContent = '点赞';
				} else {
					// 点赞
					button.classList.add('liked');
					likeCount++;
					button.querySelector('span:nth-child(2)').textContent = '已赞';
				}
				
				// 更新点赞数
				likeCountSpan.textContent = likeCount;
				
				// 移除动画类，以便下次点击可以重新添加
				setTimeout(() => {
					button.classList.remove('animating');
				}, 400);
			}
			
			// 页面加载时为每个点赞按钮添加随机初始点赞数
			document.addEventListener('DOMContentLoaded', function() {
				const likeButtons = document.querySelectorAll('.like-button');
				likeButtons.forEach(button => {
					// 生成1-100之间的随机点赞数
					const randomLikes = Math.floor(Math.random() * 100) + 1;
					const likeCountSpan = button.querySelector('.like-count');
					likeCountSpan.textContent = randomLikes;
					
					// 随机设置一些按钮为已点赞状态
					if (Math.random() > 0.7) {
						button.classList.add('liked');
						button.querySelector('span:nth-child(2)').textContent = '已赞';
					}
				});
			});
			
			// 更新文件名显示
			function updateFileName(input) {
				const fileNameDisplay = document.getElementById('file-name');
				if (input.files.length > 0) {
					// 获取文件名
					let fileName = input.files[0].name;
					// 如果文件名太长，截断显示
					if (fileName.length > 20) {
						fileName = fileName.substring(0, 17) + '...';
					}
					fileNameDisplay.textContent = fileName;
					fileNameDisplay.title = input.files[0].name; // 鼠标悬停显示完整文件名
				} else {
					fileNameDisplay.textContent = '未选择文件';
					fileNameDisplay.title = '';
				}
			}
			
			// 为自定义文件上传按钮添加点击事件
			document.addEventListener('DOMContentLoaded', function() {
				const fileUploadButton = document.querySelector('.file-upload-button');
				const fileInput = document.getElementById('weibo-picture');
				
				fileUploadButton.addEventListener('click', function() {
					fileInput.click();
				});
			});
			
			// 评论功能实现
			function toggleComment(button) {
				// 找到该博客对应的评论区域
				const blogDiv = button.closest('.blog');
				const commentSection = blogDiv.querySelector('.comment-section');
				const commentInputArea = blogDiv.querySelector('.comment-input-area');
				
				// 切换显示/隐藏状态
				if (commentSection.style.display === 'none' || commentSection.style.display === '') {
					commentSection.style.display = 'block';
					commentInputArea.style.display = 'block';
				} else {
					commentSection.style.display = 'none';
					commentInputArea.style.display = 'none';
				}
			}
			
			// 发表评论功能实现
			function submitComment(button) {
				const commentInputArea = button.closest('.comment-input-area');
				const textarea = commentInputArea.querySelector('textarea');
				const commentText = textarea.value.trim();
				
				if (commentText === '') {
					alert('请输入评论内容！');
					return;
				}
				
				// 找到对应的评论区域
				const blogDiv = commentInputArea.closest('.blog');
				const commentSection = blogDiv.querySelector('.comment-section');
				
				// 创建新评论
				const newComment = document.createElement('div');
				newComment.textContent = '当前用户：' + commentText;
				newComment.style.marginBottom = '8px';
				
				// 将新评论添加到评论区域
				commentSection.appendChild(newComment);
				
				// 清空输入框
				textarea.value = '';
				
				// 确保评论区域可见
				commentSection.style.display = 'block';
			}
		</script>
	</body>
</html>
