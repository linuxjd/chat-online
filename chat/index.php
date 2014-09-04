<?php
include_once '../includes/db_connect.php';
include_once '../includes/functions.php';
 
sec_session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>私信系统</title>
  <script type="text/javascript">
  //WebSocket = null;
  </script>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <!-- Include these three JS files: -->
  <script type="text/javascript" src="js/swfobject.js"></script>
  <script type="text/javascript" src="js/web_socket.js"></script>
  <script type="text/javascript" src="js/json.js"></script>
  <script type="text/javascript" src="js/jquery.min.js"></script>

  <script type="text/javascript">
    var name='<?php echo htmlentities($_SESSION['username']); ?>'; 
    if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
    WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
    WEB_SOCKET_DEBUG = true;
    var ws, friend="all", user_list={};
        function init() {
       // 创建websocket
       var uid = '<?php echo htmlentities($_SESSION["user_id"]);?>'
    	ws = new WebSocket("ws://"+document.domain+":7272/?uid="+uid);
      // 当socket连接打开时，输入用户名
      ws.onopen = function() {
    	  if(!name) {
    		  return ws.close();
   		  }
    	  ws.send(JSON.stringify({"type":"login","name":name}));
      };
      // 当有消息时根据消息类型显示不同信息
      ws.onmessage = function(e) {
    	  console.log(e.data);
        var data = JSON.parse(e.data);
        switch(data['type']){
              // 展示用户列表
              case 'user_list':
            	  //{"type":"user_list","user_list":[{"uid":xxx,"name":"xxx"},{"uid":xxx,"name":"xxx"}]}
            	  flush_user_list(data);
            	  break;
              // 登录
              case 'login':
                  //{"type":"login","uid":xxx,"name":"xxx","time":"xxx"}
            	  add_user_list(data['uid'], data['name']);
                  say(data['uid'], 'all',  data['name']+' 加入了聊天室', data['time']);
                  break;
              // 发言
              case 'say':
            	  //{"type":"say","from_uid":xxx,"to_uid":"all/uid","content":"xxx","time":"xxx"}
            	  say(data['from_uid'], data['to_uid'], data['content'], data['time']);
            	  break;
             // 用户退出 
              case 'logout':
            	  //{"type":"logout","uid":xxx,"time":"xxx"}
         		 say(data['uid'], 'all', user_list['_'+data['uid']]+' 退出了', data['time']);
         		 del_user_list(data['uid']);
        }
      };
      ws.onclose = function() {
    	  console.log("服务端关闭了连接");
      };
      ws.onerror = function() {
    	  console.log("出现错误");
      };
    }

    // 输入姓名
    function show_prompt(){  
        name = prompt('输入你的名字：', '');
        if(!name){  
            alert('姓名输入为空，请重新输入！');  
            show_prompt();
        }
        //name = name.replace(/\"/g,'\\"');
    }  

    // 提交对话
    function onSubmit() {
      var input = document.getElementById("textarea");
      ws.send(JSON.stringify({"type":"say","to_uid":friend,"content":input.value}));
      input.value = "";
      input.focus();
    }

    // 将用户加如到当前用户列表
    function add_user_list(uid, name){
    	user_list['_'+uid] = name;
    	flush_user_list_window();
    }

    // 删除一个用户从用户列表
    function del_user_list(uid)
    {
    	delete user_list['_'+uid];
    	flush_user_list_window();
    }

    // 刷新用户列表数据
    function flush_user_list(data){
    	user_list = {};
    	if('user_list' in data){
	    	for(var p in data['user_list']){
	   	 	    user_list['_'+data['user_list'][p]['uid']] = data['user_list'][p]['name'];
	   		}
        }
    	flush_user_list_window();
    }

    // 刷新用户列表框
    function flush_user_list_window(){
    	var userlist_window = document.getElementById("userlist");
    	userlist_window.innerHTML = '<h4>在线用户</h4><ul>';
    	for(var p in user_list){
            var str = '<li id="'+p+'"><a class="friends" href="#"' + " onclick=mytoggle('" + p +"');>"+user_list[p]+'</a></li>';
            userlist_window.innerHTML += str;
        }
    	userlist_window.innerHTML += '</ul>';
    }

    // 发言
    function say(from_uid, to_uid, content, time){
    	var dialog_window = document.getElementById("dialog"); 
    	switch(to_uid){
    		   case 'all':
    			   if(user_list['_'+from_uid]){
    				   dialog_window.innerHTML +=  '<div class="speech_item"><img src="http://lorempixel.com/38/38/?'+from_uid+'" class="user_icon" /> '+user_list['_'+from_uid]+' <br> '+time+'<div style="clear:both;"></div><p class="triangle-isosceles top">'+content+'</p> </div>';
    			   }
    			   break;
    		   // 私聊
    		   default :
    			   if(user_list['_'+from_uid]){
    				   dialog_window.innerHTML +=  '<div class="speech_item"><img src="http://lorempixel.com/38/38/?'+from_uid+'" class="user_icon" /> '+user_list['_'+from_uid]+' <br> '+time+'<div style="clear:both;"></div><p class="triangle-isosceles top">'+content+'</p> </div>';
   				   }
    	}
    }

    function mytoggle(touid){
        touid = touid.substring(1);
        friend = touid;
        //alert(touid);
    };
  </script>
</head>
<body onload="init();">
<body>
    <?php

    if (login_check($mysqli) == false){
        echo "<script>";
        echo "window.location.href='../login.php'";
        echo "</script>";
    }

    if (isset($_GET['error'])) {
            echo '<p class="error">Error Logging In!</p>';
        }
    ?> 

    <p>If you are done, please <a href="../includes/logout.php">log out</a></p>
    <p>关注更多好友<a href="../userlist.php">关注</a></p>
    <div class="container">
	    <div class="row clearfix">
	        <div class="col-md-1 column">
	        </div>
            <div class="col-md-3 column">
	           <div class="thumbnail">
                   <div class="caption" id="userlist"></div>
               </div>
	        </div>
	        <div class="col-md-6 column">
	           <div class="thumbnail">
	               <div class="caption" id="dialog"></div>
	           </div>
	           <form onsubmit="onSubmit(); return false;">
                    <textarea class="textarea thumbnail" id="textarea"></textarea>
                    <div class="say-btn"><input type="submit" class="btn btn-default" value="发表" /></div>
               </form>
	        </div>
	    </div>
    </div>
</body>
</html>
