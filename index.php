<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>XX微博主页面</title>
		<style>
			*{
				margin:0;
				padding:0;
				box-sizing:border-box;
			} 
			.container{
				width:1400px;
				margin:0 auto;
				background-color:#fdfdfd; /* 奶油白底色 */
				box-shadow: 0 0 12px rgba(0,0,0,0.02); /* 更淡阴影 */
				border-radius: 8px;
				overflow: hidden;
			}
			.header{
				padding:15px 20px;
				color:#4a5568; /* 浅灰蓝文字 */
				background-color: #f0f8fb; /* 浅天蓝背景 */
				display: flex;
				border-bottom: 1px solid #e8f4f8; /* 浅蓝分割线 */
			}
			.head-left{
				flex:3;
				margin-right:10px;
				background-color: #ffffff;
				padding: 8px 12px;
				border-radius: 6px;
			}
			.head-right{
				flex:2;
				text-align: right;
				background-color: #f0f8fb;
				padding: 8px 12px;
				border-radius: 6px;
			}
			.nav{
				padding:10px 20px;
				background-color: #ffffff;
				color:lightblue;
				border-bottom: 1px solid #e8f4f8;
				text-align: center;
			}
			.blog-area{
				text-align:center;
			}
			.blog-display{
				
			}
			.blog{
				text-align: left;
				margin: 10px 0;
				padding: 8px 0;
				border-bottom: 1px solid black; /* 黑色分割线 */
			}
			.footer{
			    text-align:center;
				padding:20px;
				background-color: #f0f8fb; /* 浅天蓝页脚 */
				color: #718096; /* 浅灰文字 */
				border-top: 1px solid #e8f4f8;
			}
			.body{
				display:flex;
				padding:15px;
				background-color: #f5fafe; /* 浅青蓝背景 */
			}
			.left-sider{
				flex:1;
				margin-right:10px;
				background-color: #ffffff;
				padding: 10px;
				border-radius: 6px;
				box-shadow: 0 1px 2px rgba(0,0,0,0.01);
			}
			.main-part{
				flex:3;
				margin-right:10px;
				background-color: #ffffff;
				padding: 15px;
				border-radius: 6px;
				box-shadow: 0 1px 2px rgba(0,0,0,0.01);
			}
			.right-sider{
				flex:1;
				background-color: #ffffff;
				padding: 10px;
				border-radius: 6px;
				box-shadow: 0 1px 2px rgba(0,0,0,0.01);
			}
			.hot-new{
				align-content: center;
				margin: 8px 0;
				padding: 4px 0;
				border-bottom: 1px solid #f5fafe;
			}
			/* 按钮清新浅色系调整 */
			#search-submit, #content-submit{
				background-color: #66d9e8; /* 薄荷浅绿按钮 */
				color: #2d3748; /* 深灰文字更清晰 */
				border: none;
				padding: 6px 12px;
				border-radius: 4px;
				cursor: pointer;
				transition: background-color 0.2s;
			}
			#search-submit:hover, #content-submit:hover{
				background-color: #3bc9db; /* hover加深一点薄荷绿 */
			}
			textarea, input[type="text"], input[type="file"]{
				border: 1px solid #e8f4f8; /* 浅蓝边框 */
				border-radius: 4px;
				padding: 6px 8px;
				background-color: #fefefe;
			}
			textarea:focus, input[type="text"]:focus{
				outline: none;
				border-color: #66d9e8; /* 聚焦薄荷绿边框 */
				box-shadow: 0 0 0 2px rgba(102, 217, 232, 0.08);
			}
			select{
				border: 1px solid #e8f4f8 !important;
				color: #4a5568;
				background-color: #fefefe;
			}
			/* 用户名和内容文字配色调整 */
			.username{
				color: #2d3748;
				font-weight: 600;
				font-size: 15px;
				margin-bottom: 6px;
				text-align: left;
			}
			.blog-content{
				color: #4a5568;
				font-size: 14px;
				line-height: 1.6;
				padding: 0 20px;
				text-align: left;
				margin-bottom: 4px;
			}
			.blog-picture {
			  margin: 8px 0; /* 与上下内容（博客内容/分割线）保持合理间距，避免拥挤 */
			  text-align: left; /* 让图片水平居中，与博客整体风格统一 */
			  padding: 0 20px; /* 左右留白，和 blog-content 保持一致，视觉协调 */
			}
			
			/* 优化图片样式：防止变形、添加美化效果 */
			.blog-picture img {
			  /* 1. 控制图片尺寸：避免过大撑破容器，同时保证清晰度 */
			  max-width: 300px; 
			  max-height: 150px; /* 限制最大高度，避免图片过高占用过多空间 */
			  width: auto; /* 宽度随高度自适应，防止图片拉伸变形 */
			  height: auto; /* 高度随宽度自适应，保持 */
			  }
			/* 兼容原有usrname类名 */
			.usrname{
				color: #2d3748;
				font-weight: 600;
				font-size: 15px;
				margin-bottom: 6px;
				text-align: center;
			}
			
			/* 点赞按钮样式 */
			.like-button {
				display: inline-block;
				margin: 8px auto 4px;
				padding: 6px 15px;
				background-color: #f8f9fa;
				border: 1px solid #e2e8f0;
				border-radius: 20px;
				color: #4a5568;
				font-size: 13px;
				cursor: pointer;
				transition: all 0.3s ease;
				user-select: none;
			}
			
			/* 点赞按钮内部图标和文本布局 */
			.like-button span {
				display: inline-block;
				vertical-align: middle;
			}
			
			/* 点赞图标样式 */
			.like-icon {
				display: inline-block;
				width: 16px;
				height: 16px;
				margin-right: 6px;
				background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234a5568'%3E%3Cpath d='M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z'/%3E%3C/svg%3E");
				background-repeat: no-repeat;
				background-position: center;
				background-size: contain;
				transition: transform 0.2s ease;
			}
			
			/* 点赞按钮悬停效果 */
			.like-button:hover {
				background-color: #f1f5f9;
				border-color: #cbd5e0;
				color: #2d3748;
			}
			
			/* 点赞按钮点击效果 */
			.like-button:active {
				transform: scale(0.95);
			}
			
			/* 点赞状态 - 已点赞样式 */
			.like-button.liked {
				background-color: #fff5f5;
				border-color: #fed7d7;
				color: #e53e3e;
			}
			
			/* 点赞状态 - 已点赞图标样式 */
			.like-button.liked .like-icon {
				background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23e53e3e'%3E%3Cpath d='M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z'/%3E%3C/svg%3E");
				transform: scale(1.1);
			}
			
			/* 点赞动画效果 */
			@keyframes likeAnimation {
				0% { transform: scale(1); }
				25% { transform: scale(1.2); }
				50% { transform: scale(0.9); }
				75% { transform: scale(1.1); }
				100% { transform: scale(1); }
			}
			
			.like-button.animating .like-icon {
				animation: likeAnimation 0.4s ease;
			}
			
			/* 点赞计数样式 */
			.like-count {
				font-weight: 600;
				margin-left: 2px;
			}
			
			/* 自定义文件上传按钮样式 */
			.file-upload-container {
				display: inline-block;
				position: relative;
				vertical-align: middle;
			}
			
			.file-upload-button {
				display: inline-block;
				padding: 6px 12px;
				background-color: #e6f7ff;
				color: #2d3748;
				border: 1px solid #bae7ff;
				border-radius: 4px;
				font-size: 13px;
				cursor: pointer;
				transition: all 0.2s ease;
			}
			
			.file-upload-button:hover {
				background-color: #d6f1ff;
				border-color: #91d5ff;
			}
			
			.file-upload-button:active {
				background-color: #bae7ff;
				transform: translateY(1px);
			}
			
			.file-upload-container input[type="file"] {
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				opacity: 0;
				cursor: pointer;
			}
			
			.file-name-display {
				display: inline-block;
				margin-left: 8px;
				padding: 4px 8px;
				font-size: 12px;
				color: #718096;
				background-color: #f8f9fa;
				border-radius: 3px;
				max-width: 150px;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
				vertical-align: middle;
			}
			
			.file-upload-icon {
				display: inline-block;
				width: 14px;
				height: 14px;
				margin-right: 4px;
				background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234a5568'%3E%3Cpath d='M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z'/%3E%3C/svg%3E");
				background-repeat: no-repeat;
				background-position: center;
				background-size: contain;
				vertical-align: text-bottom;
			}
			
			/* 评论区域样式 */
			.comment-section {
				margin-top: 8px;
				padding: 8px 20px;
				color: #4a5568;
				font-size: 13px;
				background-color: #f8f9fa;
				border-radius: 4px;
			}
			
			/* 评论输入区域样式 */
			.comment-input-area {
				margin-top: 10px;
				padding: 10px 20px;
				background-color: #fafafa;
				border-radius: 4px;
				display: none;
			}
			
			.comment-input-area textarea {
				width: 100%;
				min-height: 60px;
				margin-bottom: 8px;
				font-size: 13px;
				font-family: inherit;
				resize: vertical;
			}
			
			.comment-submit-btn {
				background-color: #66d9e8;
				color: #2d3748;
				border: none;
				padding: 6px 16px;
				border-radius: 4px;
				cursor: pointer;
				transition: background-color 0.2s;
				font-size: 13px;
			}
			
			.comment-submit-btn:hover {
				background-color: #3bc9db;
			}
		</style>
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
