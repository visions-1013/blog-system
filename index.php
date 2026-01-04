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
				text-align: center;
				margin: 10px 0;
				padding: 8px 0;
				border-bottom: 1px solid #f5fafe; /* 浅蓝灰分割线 */
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
				text-align: center;
			}
			.blog-content{
				color: #4a5568;
				font-size: 14px;
				line-height: 1.6;
				padding: 0 20px;
				text-align: center;
				margin-bottom: 4px;
			}
			.blog-picture {
			  margin: 8px 0; /* 与上下内容（博客内容/分割线）保持合理间距，避免拥挤 */
			  text-align: center; /* 让图片水平居中，与博客整体风格统一 */
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
					<br/>
					<br/>
					<br/>
					登录选项（后端）&nbsp;&nbsp;&nbsp;&nbsp;注册选项(后端)
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
						<form method="post" action="">
							<textarea placeholder="分享您的新鲜事···" rows="5" cols="30"
							name="contentInput" id="contentInput"></textarea>
							<br/>
							<input type="file" name="weibo-picture" id="weibo-picture"
							accept="image/jpeg,image/png,image/gif"/>
							话题:
							<select name="region" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #e8f4f8;">
								<option value="daily">日常生活</option>
								<option value="travel">旅行</option>
								<option value="food">美食</option>
								<option value="learning">学习</option>
								<option value="jobs">工作</option>
							</select>&nbsp;
							<input type="submit" name="content-submit" id="content-submit" value="一键分享">
						</form>
					</div>
					<div class="blog-display">
						<div class="blog">
							<div class="usrname">章航渝(神人)</div>
							<div class="blog-content">今天是昨天的明天，也就是明天的昨天.....</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
						</div>
						<div class="blog">
							<div class="usrname">三生有幸（华裔润人）</div>
							<div class="blog-content">人生第一次到纽约....真是我的精神故乡啊····</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
						</div>
						<div class="blog">
							<div class="usrname">楠楠</div>
							<div class="blog-content">今天，我们恋爱啦！请大家祝福我们！</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
						</div>
						<div class="blog">
							<div class="usrname">杭州发布</div>
							<div class="blog-content">就在刚刚！官宣NO.1</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
						</div>
						<div class="blog">
							<div class="username">用户123456</div>
							<div class="blog-content">这是我的第一条微博，记录一下今天的开心日常～</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
						</div>
						<div class="blog">
							<div class="username">银鑫城官方</div>
							<div class="blog-content">今天我们入住XX微博啦！</div>
							<div class="blog-picture">
								<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
							</div>
						</div>
					</div>
				</div>
				<div class="right-sider">
					<p>热点新闻(后端填入,应当是链接吧，可以删除)</p>
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
	</body>
</html>