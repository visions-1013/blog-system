<!DOCTYPE html>
<html>
    <head>
        <title>Search</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="static\css\search_style.css">   
    </head>
    <body>
        <div class="container">
        <div class="header">
            <div class="head-left">
            <h3>以下是您输入的内容（前端）的搜索结果</h3>
            </div>
            <div class="head-right">
                <a href="index.php" class="index_button">返回主页</a>
            </div>
        </div>


        <div class="body">

            <div class="main-part">
            
            <div class="blog">
			<div class="username">张航与 </div>
			<div class="blog-content">我是Sister Bins的好朋友</div>
			<div class="blog-picture">
				<img src="" alt="微博配图"></div>
				<button class="like-button" onclick="toggleLike(this)">
					<span class="like-icon"></span>
					<span>点赞</span>
					<span class="like-count">0</span>
				</button>
				<button class="comment-button" onclick="toggleComment(this)">
					<span class="comment-icon">💬</span>
					<span>评论</span>
				</button>
				<div class="comment-section" style="display: none;">
					哈哈哈，这也太棒了吧！！
				</div>
				<div class="comment-input-area" style="display: none;">
					<textarea placeholder="写下你的评论..."></textarea>
					<button class="comment-submit-btn" onclick="submitComment(this)">发表评论</button>
				</div>
            </div>

            <div class="blog">
			<div class="username">张航与 </div>
			<div class="blog-content">我是Sister Bins的好朋友</div>
			<div class="blog-picture">
				<img src="" alt="微博配图"></div>
				<button class="like-button" onclick="toggleLike(this)">
					<span class="like-icon"></span>
					<span>点赞</span>
					<span class="like-count">0</span>
				</button>
				<button class="comment-button" onclick="toggleComment(this)">
					<span class="comment-icon">💬</span>
					<span>评论</span>
				</button>
				<div class="comment-section" style="display: none;">
					哈哈哈，这也太棒了吧！！
				</div>
				<div class="comment-input-area" style="display: none;">
					<textarea placeholder="写下你的评论..."></textarea>
					<button class="comment-submit-btn" onclick="submitComment(this)">发表评论</button>
				</div>
            </div>


            <div class="blog">
			<div class="username">张航与 </div>
			<div class="blog-content">我是Sister Bins的好朋友</div>
			<div class="blog-picture">
				<img src="" alt="微博配图"></div>
				<button class="like-button" onclick="toggleLike(this)">
					<span class="like-icon"></span>
					<span>点赞</span>
					<span class="like-count">0</span>
				</button>
				<button class="comment-button" onclick="toggleComment(this)">
					<span class="comment-icon">💬</span>
					<span>评论</span>
				</button>
				<div class="comment-section" style="display: none;">
					哈哈哈，这也太棒了吧！！
				</div>
				<div class="comment-input-area" style="display: none;">
					<textarea placeholder="写下你的评论..."></textarea>
					<button class="comment-submit-btn" onclick="submitComment(this)">发表评论</button>
				</div>
            </div>


            <div class="blog">
			<div class="username">张航与 </div>
			<div class="blog-content">我是Sister Bins的好朋友</div>
			<div class="blog-picture">
				<img src="" alt="微博配图"></div>
				<button class="like-button" onclick="toggleLike(this)">
					<span class="like-icon"></span>
					<span>点赞</span>
					<span class="like-count">0</span>
				</button>
				<button class="comment-button" onclick="toggleComment(this)">
					<span class="comment-icon">💬</span>
					<span>评论</span>
				</button>
				<div class="comment-section" style="display: none;">
					哈哈哈，这也太棒了吧！！
				</div>
				<div class="comment-input-area" style="display: none;">
					<textarea placeholder="写下你的评论..."></textarea>
					<button class="comment-submit-btn" onclick="submitComment(this)">发表评论</button>
				</div>
            </div>

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
        
        <script>
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
