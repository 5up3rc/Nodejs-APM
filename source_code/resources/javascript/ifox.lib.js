/**
 * @description Ifox 全局对象，负责前端的交互组织
 * 
 * @namespace 全局的命名空间
 */
Ifox = window.Ifox || {};

Ifox = {
        
    history : [],
    /**
     * @description Ajax全局动作绑定
     * @return Boolean
     */
    ajaxInit : function() {
        
        // ajax完成事件回调绑定
        $(document).ajaxComplete(function(event,xhr,options) {
            if (options.async == true){
                $(".selectors").selectors({
                    callback : function(obj){
                        try{
                            if (window.selectorCb && typeof(window.selectorCb) == 'function'){
                                window.selectorCb(obj);
                            }
                        }catch(e){}
                    }
                });
            }
            $('.select').applySelect();
        });
        
        // ajax加载链接(点击时异步将a标签href属性设置的URL加载到ajaxTarget属性设置的容器中)
        $('a.ajax').die('click').live('click', function() {
            var href = $(this).attr('href');
            var ajaxTarget = $(this).attr('ajaxTarget');
            if (ajaxTarget == '#aDialog'){
                Ifox.ajaxDialog($(this));
            }else{
                Ifox.history.push({ uri : href, method : 'load'});
            }
            $(ajaxTarget).load(href);
            return false;
        });
        
        // Ajax时不要缓存 
        $.ajaxSetup({ cache: false });
        
        // Form表单Ajax提交时
        $('form.ajax').die('submit').live('submit', function(){
            // 如果设置了验证回调函数,则先执行验证回调函数,验证回调函数返回true表示验证通过
            if ($(this).data('validateCallback') && typeof ($(this).data('validateCallback')) == 'function' && !$(this).data('validateCallback')()) {
                return false;
            }
            var ajaxTarget = $(this).attr('ajaxTarget');
            if (ajaxTarget == undefined) {
                if ($(this).attr('method') && ($(this).attr('method').toLowerCase() == 'post')) {
                    $.post($(this).attr('action'), $(this).serialize(), Ifox.ajaxCb, 'json');
                } else {
                    $.get($(this).attr('action'), $(this).serialize(), Ifox.ajaxCb, 'json');
                }
            } else {
                var uri;
                $(ajaxTarget).load($(this).attr('action'), $(this).serialize(), Ifox.ajaxCb);
                if ($(this).attr('action').indexOf('?') == -1){
                    uri = $(this).attr('action') + "?" + $(this).serialize();
                }else{
                    uri = $(this).attr('action') + "&" + $(this).serialize();
                }
                Ifox.history.push({ uri : uri, method : 'load'});
            }
            return false;
        });
        // ajax确认操作链接(点击时异步GET调用A标签href属性设置的URL,通常用于删除链接等)
        $('a.ajaxConfirm').die('click').live('click', function() {
            if (window.confirm('确定要执行此操作吗？')) {
                $.get($(this).attr('href'), {}, Ifox.ajaxCb, 'json');
            }
            return false;
        });
    },
    
    // Ajax加载弹出窗口时
    ajaxDialog : function(ele){
        $('body').append('<div id="aDialog"></div>');
        $('#aDialog').dialog({
            height : ele.attr('height') || 500,
            width : ele.attr('width') || 700, 
            modal : ele.attr('modal') || true, //蒙层（弹出会影响页面大小）
            title : ele.attr('title') || '默认 弹窗',
            resizable : true
        });
    },
    
    ajaxTable : function(uri, params ){
        // 表格Even和Odd
        $('table tbody').find('tr:even').addClass('even');
        $('table tbody').find('tr:odd').addClass('odd');
        
        // 默认排序分页
        $('table.mui-data-table th').each(function(){
            $(this).html($(this).html() + "<i class='mui-global-fr'></i>");
            if (params.hasOwnProperty('sort_field') == true && params.sort_field == $(this).attr('name')){
                var arrow;
                if (params.hasOwnProperty('sort') == true && params.sort == 'ASC'){
                    arrow = 'awe-icon-chevron-down';
                }else{
                    arrow = 'awe-icon-chevron-up';
                }
                $(this).find('i').addClass(arrow);
                var idx = $(this).index();
                $('table.mui-data-table tr').each(function(){
                    $(this).find( 'td:eq('+idx+')').addClass('sorting');
                });
            }
        }).die('click').live('click', function(){ // 排序动作
            if ($(this).attr('name') == undefined){
                return false;
            }
            params.sort_field = $(this).attr('name');
            params.sort = (params.sort == 'ASC') ? 'DESC' : 'ASC';
            if (uri.indexOf('?') == -1){
                uri += '?';
            }else{
                uri += '&';
            }
            $('#right-cont').load(uri + Ifox.httpBuildQuery(params));
            Ifox.history.push({ uri : uri + Ifox.httpBuildQuery(params), method : 'load'});
        });
        // 翻页动作
        $('.mui-pagination a').die('click').live('click', function(){
            if ($(this).attr('page') == undefined){
                return false;
            }
            params.page = $(this).attr('page');
            if (uri.indexOf('?') == -1){
                uri += '?';
            }else{
                uri += '&';
            }
            $('#right-cont').load(uri + Ifox.httpBuildQuery(params));
            Ifox.history.push({ uri : uri + Ifox.httpBuildQuery(params), method : 'load'});
        });
    },
    
    /**
     * ajax回退处理函数
     */
    ajaxGoBack: function(step) {
        if (Ifox.history.length <= 0){
            return ;
        }
        step = Ifox.history.length - step -1;
        if (step < 0){
            step == 0;
        }
        var obj = Ifox.history[step];
        if (obj.method == 'load'){
            $('#right-cont').load(obj.uri);
            Ifox.history.push({ uri : obj.uri, method : 'load'});
        }
        if (obj.method == 'get'){
            $.get(obj.uri, obj.params, Ifox.ajaxCb, 'json');
        }
        if (obj.method == 'post'){
            $.post(obj.uri, obj.params, Ifox.ajaxCb, 'json');
        }
    },
    
    /**
     * ajax成功回调函数
     */
    ajaxCb: function(response, textStatus, xhr) {
        if (response.hasOwnProperty('msg')) { // 有指定回调方法
            try{
                if (window.ajax.callback && typeof(window.ajax.callback) == 'function'){
                    window.ajax.callback(response, textStatus, xhr); return false;
                }
            }catch(e){}
            if (response.msg.act == 'refresh') { // 刷新
                Ifox.ajaxGoBack(0);
            } else if (response.msg.act == 'error'){ // 显示错误信息
                $('.error').find('.tips').html(response.msg.data);
                $('.error').show();
            } else if (response.msg.act == 'close') { // 关闭弹框
                $('#aDialog').dialog('close');
                Ifox.ajaxGoBack(0);
            } else if (response.msg.act == 'alert'){
                alert(response.msg.data);
            }
        }
    },

    /**
     * 去除前后空格
     */
    trim : function(string) {
        return string.replace(/(^\s*)|(\s*$)/g, "");
    },
    /**
     * 去除前空格
     */
    ltrim : function(string) {
        return string.replace(/(^\s*)/g, "");
    },
    /**
     * 去除后空格
     */
    rtrim : function(string) {
        return string.replace(/(\s*$)/g, "");
    },

    /**
     * 解析URL成对象
     * 
     * @param url
     * @param type
     * @returns
     */
    parseUrl : function(url) {
        if (url.length <= 0) {
            return false;
        }
        // 返回结果对象
        var url_object = {
            'scheme' : '', 'host' : '', 'api' : '', 'params' : {}
        };
        // 判断协议
        if (url.indexOf('http://') >= 0) {
            url_object['scheme'] = 'http://';
            url = url.replace('http://', '');
        } else if (url.indexOf('https://') >= 0) {
            url_object['scheme'] = 'https://';
            url = url.replace('https://', '');
        }
        if (url.indexOf('?') < 0 && url.indexOf('/') < 0) {
            return url_object;
        }
        if (url.indexOf('?') > 0) {
            var url_arr = url.split("?");
            if (url_arr[0].indexOf('/') > 0) {
                var position = url_arr[0].indexOf('/');
                url_object['host'] = url_arr[0].substring(0, position);
                url_object['api'] = url_arr[0].substr(position);
            }
            var params_arr = url_arr[1].split('&');
            url_object['params'] = {};
            for ( var i in params_arr) {
                var value_arr = params_arr[i].split("=");
                url_object['params'][value_arr[0]] = value_arr[1];
            }

        } else {
            if (url.indexOf('/') > 0) {
                var position = url.indexOf('/');
                url_object['host'] = url.substring(0, position);
                url_object['api'] = url.substr(position);
            }
        }
        return url_object;
    },

    /**
     * 组装URL参数部分
     * 
     * @param params
     * @returns
     */
    httpBuildQuery : function(params) {
        if (params == null) {
            return false;
        }
        var url_arr = [];
        $.each(params, function(key, value) {
            url_arr.push(key + '=' + value);

        });
        var url_query = url_arr.join("&");
        return url_query;
    }
};