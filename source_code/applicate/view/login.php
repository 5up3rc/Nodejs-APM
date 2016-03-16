<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>管理后台系统</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=8" />
<!-- CSS -->
    <link href="/resources/css/layout.css" rel="stylesheet" type="text/css"></link>
<!-- Javascript -->
    <script type="text/javascript" src="/resources/javascript/jquery-1.8.2.min.js"></script>
<!-- Favicons -->
    <link rel="shortcut icon" href="/resources/images/favicon.ico" ></link>
    <style type="text/css">
    body {font-size:14px;}
    .block-heading{
        line-height: 19px;
        padding: 10px 15px;
        color: #444;
        -webkit-box-shadow: inset 0px 1px 0 rgba(255, 255, 255, 1.0);
        -moz-box-shadow: inset 0px 1px 0 rgba(255, 255, 255, 1.0);
        box-shadow: inset 0px 1px 0 rgba(255, 255, 255, 1.0);
        background: -webkit-gradient(linear, 0 0, 0 100%, from(#fafafa), to(#f3f3f3));
        background: -moz-linear-gradient(top,#fafafa,#f3f3f3);
        background: -ms-linear-gradient(top,#fafafa,#f3f3f3);
        text-align:left;
    }
    .block-body{
        padding: 1em;
        min-height: .25em;
        background:#FFF;
        border-top:1px solid #CCCCCC;
    }
    .block-body input{
        width: 358px;
    }
    .block-body button{
        background: #B4D66F;
        background-image: linear-gradient(to bottom, #B4D66F, #88AB4A);
        border : 1px solid #56740F;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
        cursor: pointer;
        font-family: "微软雅黑", "宋体";
        line-height: 25px;
        font-weight:bold;
        padding: 2px 15px;
    }
    </style>
</head>
<body>
<div style="width:400px; margin:100px auto 0px auto; border:1px solid #CCCCCC;">
    <p class="block-heading"><b>管理后台系统登入</b></p>
    <div class="block-body">
        <form name="loginForm" method="post" action="/login/dologin">
            <div style="line-height:30px;">账号</div>
            <input type="text" class="input-text" name="user_name" required="true" autofocus="true">
            <div style="line-height:30px;">密码</div>
            <input type="password" class="input-text" name="password" required="true" >
            <div style="line-height:30px; text-align:right; margin-top:20px;">
                <span class="red err_msg"></span>
                <button type="button" class="ta_btn ta_btn_primary">登入</button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $(document).keyup(function(event){
        if (event.keyCode == 13){
            $(".ta_btn").trigger('click');
        }
    });
    $(".ta_btn").click(function(){
        $.ajax({
            type : 'POST',
            url : '/login/dologin',
            data : { 'user_name' : $('input[name=user_name]').val(), 'password' : md5($("input[type=password]").val()) },
            dataType : "json",
            success : function(response) {
                console.log(response);
                if (response.code == 1){
                    $(".err_msg").html(response.msg);
                }else{
                    window.location.href = response.msg.index;
                    return false;
                }
            }
        })
    })
})
</script>
</body>
</html>