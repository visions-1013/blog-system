<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>页面标题</title>
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
		.main-part{
			flex:4;
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
        
        /* 新增导航栏样式 */
        .nav{
            background-color: #ffffff;
            border-bottom: 1px solid #e8f4f8;
            padding: 0 20px;
        }
        .nav ul{
            display: flex;
            list-style: none;
        }
        .nav li{
            padding: 12px 18px;
        }
        .nav a{
            text-decoration: none;
            color: #4a5568;
            font-size: 14px;
            transition: color 0.2s;
        }
        .nav a:hover{
            color: #3182ce;
        }
        .nav li.active{
            border-bottom: 2px solid #3182ce;
        }
        .nav li.active a{
            color: #3182ce;
            font-weight: 500;
        }
        
        /* 内容发布区域样式 */
        .blog-area{
            padding: 15px;
            background-color: #f9fafc;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        #contentInput{
            width: 100%;
            border: 1px solid #e8f4f8;
            border-radius: 6px;
            padding: 10px;
            resize: vertical;
            font-size: 14px;
            color: #4a5568;
            margin-bottom: 10px;
        }
        #contentInput:focus{
            outline: none;
            border-color: #b3e0f2;
            box-shadow: 0 0 0 2px rgba(49, 130, 206, 0.1);
        }
        input[type="file"]{
            margin-bottom: 10px;
            color: #718096;
            font-size: 14px;
        }
        input[type="submit"]{
            background-color: #3182ce;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        input[type="submit"]:hover{
            background-color: #2b6cb0;
        }
        
        /* 内容展示区域样式 */
        .blog-display{
            padding: 15px;
        }
        .blog-display p{
            color: #4a5568;
            font-size: 16px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e8f4f8;
        }
        .blog{
            padding: 15px;
            border-radius: 6px;
            background-color: #f9fafc;
            margin-bottom: 15px;
        }
        .data{
            color: #718096;
            font-size: 12px;
            margin-bottom: 8px;
        }
        .blog-content{
            color: #2d3748;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .blog-picture img{
            max-width: 300px;
            max-height: 150px; /* 限制最大高度，避免图片过高占用过多空间 */
            width: auto; /* 宽度随高度自适应，防止图片拉伸变形 */
            height: auto; /* 高度随宽度自适应，保持 */
        }
        
        /* 右侧边栏样式 */
        .right-sider h4{
            color: #4a5568;
            font-size: 15px;
            margin: 15px 0 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e8f4f8;
        }
        .username{
            color: #2d3748;
            font-size: 14px;
            padding: 6px 8px;
            border-radius: 4px;
            margin-bottom: 5px;
            transition: background-color 0.2s;
        }
        .username:hover{
            background-color: #f0f8fb;
            cursor: pointer;
        }
        .following, .Frequently{
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- 主容器 -->
    <div class="container">
        <div class="header">
            <div class="head-left">
                <h3>亲爱的（用户名称），欢迎回来!</h3>
            </div>
            <div class="head-right">
                <br/><br/><br/>退出登录（后端）
            </div>
        </div>

        <!-- 导航栏区域 -->
        <div class="nav">
            <ul>
                <li class="active"><a href="#">首页</a></li>
                <li><a href="#">发现</a></li>
                <li><a href="#">消息</a></li>
                <li><a href="#">收藏</a></li>
                <li><a href="#">设置</a></li>
            </ul>
        </div>

        <div class="body">
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
                    <p>您的往期发布</p>
					<div class="blog">
						<div class="data">2023-10-15 14:30</div>
						<div class="blog-content">今天是昨天的明天，也就是明天的昨天.....</div>
						<div class="blog-picture">
							<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
						</div>
					</div>
					<div class="blog">
						<div class="data">2023-10-10 09:15</div>
						<div class="blog-content">人生第一次到纽约....真是我的精神故乡啊····</div>
						<div class="blog-picture">
							<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
						</div>
					</div>
					<div class="blog">
						<div class="data">2023-09-28 18:45</div>
						<div class="blog-content">今天，我们恋爱啦！请大家祝福我们！</div>
						<div class="blog-picture">
							<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
						</div>
					</div>
                </div>
            </div>

            <!-- 右侧边栏 -->
            <div class="right-sider">
                <h4>你的关注</h4>
				<div class="following">
				<div class="username">东北御姐</div>
				<div class="username">宇 少将</div>
				<div class="username">安徽秀才</div>
				<div class="username">杭州小航</div>
				<div class="username">重庆小渝</div>
				</div>
				
				<h4>常看</h4>
				<div class="Frequently">
					<div class="username">白月光</div>
					<div class="username">小和山本地生活</div>
					<div class="username">记录我的美国生活（润人）</div>
					</div>
				<h4>推荐关注</h4>
				<div class="recommend">
					<div class="username">科技前沿</div>
					<div class="username">美食探店</div>
				</div>
            </div>
        </div>
    </div>

    <div class="footer">
        © 2026 XX微博 版权所有 | 隐私政策 | 联系我们<br>
            开发者团队：章航渝、章晨阳、周凯涵<br>
            本页面为自制微博前端演示，后端功能待后续开发
    </div>
</body>
</html>