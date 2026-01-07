/**
 * AJAX请求封装函数
 * @param {string} method - 请求方法 ('GET' 或 'POST')
 * @param {string} url - 请求URL
 * @param {object|FormData} data - 发送的数据（普通对象或FormData）
 * @param {function} callback - 回调函数，接收两个参数：error, response
 * @param {boolean} isFormData - 是否为FormData（用于文件上传）
 */
function ajaxRequest(method, url, data, callback, isFormData = false) {
    // 创建XMLHttpRequest对象
    const xhr = new XMLHttpRequest();
    
    // 监听请求状态变化
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    // 解析JSON响应
                    const response = JSON.parse(xhr.responseText);
                    callback(null, response);
                } catch (e) {
                    // JSON解析失败
                    console.error('JSON解析错误:', e);
                    console.error('响应内容:', xhr.responseText);
                    callback('响应数据格式错误', null);
                }
            } else {
                // 请求失败
                console.error('请求失败:', xhr.status, xhr.statusText);
                console.error('响应内容:', xhr.responseText);
                callback('请求失败: ' + xhr.status, null);
            }
        }
    };
    
    // 监听网络错误
    xhr.onerror = function() {
        console.error('网络错误');
        callback('网络错误，请检查网络连接', null);
    };
    
    // 发送请求
    if (method === 'GET' && data && !isFormData) {
        // GET请求：将数据转换为URL参数并拼接到URL
        const params = Object.keys(data).map(key => 
            encodeURIComponent(key) + '=' + encodeURIComponent(data[key])
        ).join('&');
        const fullUrl = url + '?' + params;
        console.log('GET请求URL:', fullUrl);
        xhr.open(method, fullUrl, true);
        xhr.send(null);
    } else {
        // 初始化请求（POST或FormData）
        xhr.open(method, url, true);
        
        // 设置请求头（如果不是FormData）
        if (!isFormData) {
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
        
        if (isFormData) {
            // FormData（文件上传）
            xhr.send(data);
        } else if (data) {
            // POST请求（普通数据）
            const params = Object.keys(data).map(key => 
                encodeURIComponent(key) + '=' + encodeURIComponent(data[key])
            ).join('&');
            xhr.send(params);
        } else {
            xhr.send(null);
        }
    }
}

/**
 * 显示提示信息（用于调试）
 * @param {string} message - 提示信息
 * @param {string} type - 类型 ('success', 'error', 'info')
 */
function showMessage(message, type = 'info') {
    // 简单使用alert，实际项目中可以使用更美观的提示组件
    if (type === 'error') {
        console.error(message);
        alert('错误: ' + message);
    } else {
        console.log(message);
    }
}
