<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Chat exampler</title>
	<style type="text/css">
:root {
  --body-bg: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  --msger-bg: #fff;
  --border: 2px solid #ddd;
  --left-msg-bg: #ececec;
  --right-msg-bg: #579ffb;
}

html {
  box-sizing: border-box;
}

*,
*:before,
*:after {
  margin: 0;
  padding: 0;
  box-sizing: inherit;
}

body {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-image: var(--body-bg);
  font-family: Helvetica, sans-serif;
}

.msger {
  display: flex;
  flex-flow: column wrap;
  justify-content: space-between;
  width: 100%;
  max-width: 867px;
  margin: 25px 10px;
  height: calc(100% - 50px);
  border: var(--border);
  border-radius: 5px;
  background: var(--msger-bg);
  box-shadow: 0 15px 15px -5px rgba(0, 0, 0, 0.2);
}

.msger-header {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border-bottom: var(--border);
  background: #eee;
  color: #666;
}

.msger-chat {
  flex: 1;
  overflow-y: auto;
  padding: 10px;
}
.msger-chat::-webkit-scrollbar {
  width: 6px;
}
.msger-chat::-webkit-scrollbar-track {
  background: #ddd;
}
.msger-chat::-webkit-scrollbar-thumb {
  background: #bdbdbd;
}
.msg {
  display: flex;
  align-items: flex-end;
  margin-bottom: 10px;
}
.msg:last-of-type {
  margin: 0;
}
.msg-img {
  width: 50px;
  height: 50px;
  margin-right: 10px;
  background: #ddd;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  border-radius: 50%;
}
.msg-bubble {
  max-width: 450px;
  padding: 15px;
  border-radius: 15px;
  background: var(--left-msg-bg);
}
.msg-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}
.msg-info-name {
  margin-right: 10px;
  font-weight: bold;
}
.msg-info-time {
  font-size: 0.85em;
}

.left-msg .msg-bubble {
  border-bottom-left-radius: 0;
}

.right-msg {
  flex-direction: row-reverse;
}
.right-msg .msg-bubble {
  background: var(--right-msg-bg);
  color: #fff;
  border-bottom-right-radius: 0;
}
.right-msg .msg-img {
  margin: 0 0 0 10px;
}

.center-msg .msg-bubble {
	background: none !important;
}

.center-msg {
	margin: 5px 5px 5px 5px;
	text-align: center;
}

.msger-inputarea {
  display: flex;
  padding: 10px;
  border-top: var(--border);
  background: #eee;
}
.msger-inputarea * {
  padding: 10px;
  border: none;
  border-radius: 3px;
  font-size: 1em;
}
.msger-input {
  flex: 1;
  background: #ddd;
}
.msger-send-btn {
  margin-left: 10px;
  background: rgb(0, 196, 65);
  color: #fff;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.23s;
}
.msger-send-btn:hover {
  background: rgb(0, 180, 50);
}

.msger-chat {
  background-color: #fcfcfe;
}
#onlineUsers {
	list-style-type:none;
}

#onlineUsers  {
	background-color: #026702;
	color: #fff;
	margin: 4px;
}

#onlineUsers  a {
	color: white;
    margin: 2px;
    padding: 2px;
    text-decoration: none;
}
.bar {
    height: 18px;
    background: green;
}

#btnFile {
  margin-left: 10px;
  background: rgb(128, 128, 128);
  color: #fff;
   -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.23s;
}
#btnFile:hover {
  background: rgb(87, 159, 251);
}

	</style>
</head>
<body>
<section class="msger">
  <header class="msger-header">
    <div class="msger-header-title">
      <i class="fas fa-comment-alt"></i> Welccome <span id="username"></span>
    </div>
    <div class="msger-header-options">
      <span><ul class="nav nav-pills nav-stacked col-md-2" id="onlineUsers">
        </ul></span>
    </div>
  </header>
 
  <main class="msger-chat">
    

    <div class="msg right-msg">
      
    </div>
  </main>

  <form class="msger-inputarea">
    <input type="text" class="msger-input" placeholder="Enter your message...">
     <input type="text" class="msger-name" style="display:none" placeholder="Enter your username">
     <div id="btnFile" onclick="getFile()">Files</div>
     <div style='height: 0px;width: 0px; overflow:hidden;'><input type="file" class="msger-file" id="files"/></div>
    <button type="submit" class="msger-send-btn">Send</button>
  </form>
</section>

<script type="text/javascript" src="asset/jquery-3.4.1.min.js"></script>

<script type="text/javascript">
var conn = new WebSocket('ws://localhost:9191');
var username = $('#username').html();
var img = '';
const OTHER_IMG = "https://image.flaticon.com/icons/svg/327/327779.svg";
const PERSON_IMG = "https://image.flaticon.com/icons/svg/145/145867.svg";
var _msg = {};
hideChat();


function getFile()
{
	$('.msger-file').click();
}
$('#files').change(function(){
	console.log($(this).val());
	var msgText='';
	files = $(this).val();
	 if (files && files[0]) {
    
    var FR= new FileReader();
    FR.onloadend = function(){
    	var b64 = FR.result;
    	 conn.send("{\"type\":\"message\",\"name\":\"" + username + "\",\"message\":\"" + b64 + "\"}");
    	 var n = b64.indexOf('/');
    	 var _dat = b64.substr(0, n);
    	 var getType = _dat.split(':');
    	 console.log(getType);
    	 if(getType[1] == 'image')
    	 {
    	 	appendMessage(username, PERSON_IMG, 'right', '<img src="'+ b64 +'" widht="50" height="50">');
    	 }else{
    	 	appendMessage(username, PERSON_IMG, 'right', '<a href="'+ b64 +'" target="BLANK">file</a>');	
    	 }
    }
    
    FR.readAsDataURL( this.files[0] );
    $(this).val("");
  }
});

conn.onopen = function(e) {
    console.log("Connection established!");3

	
};

function hideChat(fungsi=true) {
	if(fungsi == true)
	{
	    $(".msger-input").attr('style', 'display:none');
	    $(".msger-name").attr('style', 'display:block');
	    $("#btnFile").attr('style', 'display:none');
	    $(".msger-send-btn").html('LOGIN');
	}else{
		$(".msger-input").attr('style', 'display:block');
	    $(".msger-name").attr('style', 'display:none');
	    $("#btnFile").attr('style', 'display:block');
	    $(".msger-send-btn").html('SEND');
	   
	}
}

function msg(fungsi, msg=''){
	if(fungsi == 'daftar')
	{
		return JSON.stringify(_msg);
	}else{
		return _msg;
	}
}
conn.onmessage = function(e) {
	var jsonMessage = JSON.parse(e.data);
	 console.log(jsonMessage);
	 if (jsonMessage.type === "message") {
        appendMessage(jsonMessage.name, OTHER_IMG, 'left', jsonMessage.message);
    } else if (jsonMessage.type === "onlineUsers") {
        var count = 0;
        var onlineUsers = "";
        var userChat = "";
        $.each(jsonMessage.onlineUsers, function (key, val) {
            if(username == val){
                $('#username').html(username);
            }
            if(username != val){
                if (count === 0) {
                    onlineUsers ='<li class="active"><a href="#tab_'+key+'" data-toggle="pill">'+val+'</a></li>';
                    userChat = '<div class="tab-pane active" id="tab_'+key+'"><h5>'+val+' is online</h5><div class="msg-container" id="tab_messages_'+key+'"></div><textarea id="message" class="form-control" onkeyup="sendMessage(event,'+key+')" placeholder="Type your message here..."></textarea></div>';
               } else {
                   onlineUsers = onlineUsers + '<li><a href="#tab_'+key+'" data-toggle="pill">'+val+'</a></li>';
                   userChat = userChat+'<div class="tab-pane" id="tab_'+key+'"><h5>'+val+'  is online</h5><div class="msg-container" id="tab_messages_'+key+'"></div><textarea id="message" class="form-control" onkeyup="sendMessage(event,'+key+')" placeholder="Type your message here..."></textarea></div>';
               }
               count++;
            }
        });
        //console.log(onlineUsers);
        document.getElementById('onlineUsers').innerHTML = onlineUsers;
    }
};


$('.msger-inputarea').submit(function(event) {
  event.preventDefault();

  const msgerName = $(".msger-name").val();
  const msgText = $('.msger-input').val();
  console.log(msgerName);
  if(msgText != '')
  {
	  appendMessage(username, PERSON_IMG, "right", msgText);
	  conn.send("{\"type\":\"message\",\"name\":\"" + username + "\",\"message\":\"" + msgText + "\"}");
 	  $('.msger-input').val('');
  }else if(msgerName != ''){
  	  username = msgerName;
	  conn.send("{\"type\":\"login\",\"name\":\"" + username + "\"}");
	  $('#username').html(username);
	  hideChat(false);
  } 

});

function appendMessage(name, img, side, text) {
  //   Simple solution for small apps
  const msgHTML = `
    <div class="msg ${side}-msg">

      <div class="msg-img" style="background-image: url(${img})"></div>

      <div class="msg-bubble">
        <div class="msg-info">
          <div class="msg-info-name">${name}</div>
          <div class="msg-info-time">${formatDate(new Date())}</div>
        </div>

        <div class="msg-text">${text}</div>
      </div>
    </div>
  `;

  $(".msger-chat").append(msgHTML);
  $(".msger-chat").scrollTop += 500;
}

function formatDate(date) {
  const h = "0" + date.getHours();
  const m = "0" + date.getMinutes();

  return `${h.slice(-2)}:${m.slice(-2)}`;
}


</script>
</body>
</html>