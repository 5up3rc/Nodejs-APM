<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=8" />
<!-- CSS -->
    <link href="/resources/css/layout.css" rel="stylesheet" type="text/css"></link>
<!-- Javascript -->
    <script type="text/javascript" src="/resources/javascript/jquery-1.8.2.min.js"></script>
<!-- Favicons -->
    <link rel="shortcut icon" href="/resources/images/favicon.ico" ></link>
</head>
<script type="text/javascript">
    window.setTimeout(function(){
        window.location.href="<?php echo $url; ?>";
    }, 1000);

    $(".goahead").click(function(){
        window.location.href =  document.referrer;
    });
</script>
<body style='background:#FFFFFF;'>
    <div style="width:600px; margin:0px auto; padding-top:120px;">
        <div style="text-align: center;"><img src="/resources/images/loading.gif" /></div>
        <div style="text-align: center;">
            <h3><?php echo $msg; ?></h3>
            <p style="margin-top:30px;">如果你的浏览器没有响应，<a href="javascript:void(0);" class="blue">请点击这里</a></p>
        </div>
    </div>
</body>
</html>