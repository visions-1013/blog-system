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
					<form method="post" action="">
						<input type="text" name="search" id="search" placeholder="搜索您想看的blog..."/>
						<input type="submit" name="search-submit" id="search-submit" value="点击搜索"/>
					</form>
				</div>
				<div class="head-right">

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
						<div class="blog">
							<div class="username">章航渝(神人)</div>
							<div class="blog-content">今天是昨天的明天，也就是明天的昨天.....</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
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
								章航渝的粉丝-甜妹：哈哈哈，这也太棒了吧！！（这里用于插入后端数据库中的代码）
							</div>
							<div class="comment-input-area" style="display: none;">
								<textarea placeholder="写下你的评论..."></textarea>
								<button class="comment-submit-btn" onclick="submitComment(this)">发表评论</button>
							</div>
						</div>
						<div class="blog">
							<div class="username">三生有幸（华裔润人）</div>
							<div class="blog-content">人生第一次到纽约....真是我的精神故乡啊····</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
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
							<div class="username">小确幸（华裔润人）</div>
							<div class="blog-content">人生第一次到柬埔寨....真是我的精神故乡啊····</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
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
							<div class="username">阳阳&&楠楠</div>
							<div class="blog-content">今天，我们恋爱啦！请大家祝福我们！</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
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
							<div class="username">杭州发布</div>
							<div class="blog-content">就在刚刚！官宣NO.1</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
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
							<div class="username">用户123456</div>
							<div class="blog-content">这是我的第一条微博，记录一下今天的开心日常～</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
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
							<div class="username">银鑫城官方</div>
							<div class="blog-content">今天我们入住XX微博啦！</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
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
							<div class="username">东北御姐（你的关注）</div>
							<div class="blog-content">今天我们入住XX微博啦！</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
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
