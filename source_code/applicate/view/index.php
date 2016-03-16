<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title><?php echo $data['title'];?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=8" />
    <meta HTTP-EQUIV="pragma" CONTENT="no-cache">
    <meta HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
    <meta HTTP-EQUIV="expires" CONTENT="0">
<!-- CSS -->
    <link href="/resources/css/layout.css" rel="stylesheet" type="text/css"></link>
    <link href="/resources/css/plugin.css" rel="stylesheet" type="text/css"></link>
    <link href="/resources/css/jquery.select.css" rel="stylesheet" type="text/css" />
    <link href="/resources/css/ui.css?v=<?php echo time();?>" rel="stylesheet" type="text/css"></link>
<!-- Javascript -->
    <script type="text/javascript" src="/resources/javascript/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="/resources/javascript/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/resources/javascript/daterange.plugins.js"></script>
    <script type="text/javascript" src="/resources/javascript/selectors.plugins.js"></script>
    <script type="text/javascript" src="/resources/javascript/ifox.lib.js"></script>
    <script type="text/javascript" src="/resources/javascript/highcharts.js"></script>
    <script type="text/javascript" src="/resources/javascript/chart.js"></script>
    <script type="text/javascript" src="/resources/javascript/jquery.select.js"></script>
<!-- Favicons -->
    <link rel="shortcut icon" href="/resources/images/favicon.ico" ></link>
</head>
<script type="text/javascript">
$(function(){
    $(window).resize(function(){
        var widow_width = $(".mui-layout").width();
        var left_width = $(".mui-layout-left").outerWidth(true);
        var width = widow_width - left_width - 1;
        var height = $("body").height()- 100;
        $(".mui-layout-right").css("width", width+"px");
        $(".mui-layout-left").css("min-height", height+"px");
    }).trigger("resize");
});
</script>
<body>
<div class='doc'>
    <!-- 顶部Logo和总菜单条 开始 -->
    <table class="mui-nav-top">
        <tr>
            <td class="mui-logo"><a class="mui-global-num">后台管理系统</a></td>
            <td>
                <?php foreach ($data['navigate'] as $key => $value){ ?>
                <a class="mui-nav-item <?php echo $value['navigate_id'] == $data['navigate_id'] ? 'mui-cursor-nav':'';?>" href="<?php echo $value['navigate_url']?>">
                    <i class='<?php echo $value['navigate_icon']; ?> white'></i><?php echo $value['navigate_name']; ?>
                </a>
                <?php } ?>
            </td>
            <td class='mui-user-info'>
                <a class='ajax priv' ajaxTarget='#aDialog' href="/admin/modify?method=modify&user_id=<?php echo Cool::session()->get_data('user_id');?>">
                    <i class='awe-icon-user white'></i><?php echo Cool::session()->get_data('user_name'); ?>
                </a>
                <a href="/login/logout">[注销]</a>
            </td>
        </tr>
    </table>
    <!-- 顶部Logo和总菜单条 结束 -->

    <!-- 中间部分Layout 开始 -->
    <div class="mui-layout mui-global-br">
        <!-- Sidebar功能列表 开始 -->
        <div class="mui-layout-left mui-global-br">
            <?php foreach ($data['sidebar'] as $module_id => $module) { ?>
            <div class="mui-sidebar-model">
                <b class="mui-sidebar-title">
                    <a href="javascript:void(0);">
                        <i class="<?php echo $module['module_icon']; ?>"></i>
                        <?php echo $module['module_name']; ?>
                        <i class="awe-icon-chevron-up mui-sort"></i>
                    </a>
                </b>
                <div class="mui-sider-item">
                    <?php foreach ( $module['menu'] as $key => $items ) { ?>
                    <a href="<?php echo $items['menu_url']; ?>">
                        <span><?php echo $items['menu_name']; ?></span>
                    </a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
        <script type="text/javascript">
            $(function(){
                var title = 'mui-sidebar-title', child = 'mui-sider-item';
                $("." + title).live('click', function() {
                    var childObj = $(this).next("." + child);
                    if (childObj.css('display') == 'none') {
                        $(this).find('.awe-icon-chevron-down').removeClass('awe-icon-chevron-down').addClass('awe-icon-chevron-up');
                        childObj.show(300);
                    } else {
                        $(this).find('.awe-icon-chevron-up').removeClass('awe-icon-chevron-up').addClass('awe-icon-chevron-down');
                        childObj.hide(300);
                    }
                });
                // SideBar动作绑定
                $('.mui-sider-item').find('a').die('click').live('click', function(){
                    $(".mui-layout-right").load($(this).attr('href'));
                    $('.mui-sidebar-cur').removeClass('mui-sidebar-cur');
                    $(this).addClass('mui-sidebar-cur');
                    Ifox.history.push({ uri : $(this).attr('href'), method : 'load'});
                    return false;
                });
                // 默认加载执行一次动作
                $('.mui-sider-item > a').eq(0).trigger('click');
            });
        </script>
        <!-- Sidebar功能列表 开始 -->
        <!-- 右边主内容区 开始 -->
        <div class="mui-layout-right" id='right-cont'></div>
        <!-- 右边主内容区 结束 -->
        <div style='clear: both;'></div>
    </div>
    <!-- 中间部分Layout 结束 -->
    <div class='mui-global-bb'></div>
</div>
<script type="text/javascript">
$(function(){
    // 初始化Ajax框架
    Ifox.ajaxInit();
});
</script>
</body>
</html>