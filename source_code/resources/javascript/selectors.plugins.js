/**
 * =======================================================================
 * @description 所有checkbox, radio,select,muti-select 的效果扩展组件
 * @param {object} options 配置数组
 * @author ：seven.leng(seven.leng@top25.cn)
 * @version ： 2014-09-04
 * @modification list：
 * =======================================================================
 */
;(function($) {
    $.fn.selectors = function(defaults) {
        // 当使用Class来取对象时，需要循环去处理HTML
        return this.each(function(i, element){
            var options = $.extend({
                type : 'radio',     // 类型 默认Radio
                data : '',          // 数据内容
                value : '',         // 选中的数据
                name : '',          // 表单的name
                required : 'false', // input require 属性
                height : '340',     // popup 默认高度
                width : '600',      // popup 默认宽度
                multi : 0,          // 是否多选 针对dropdown
                callback : function(obj) {
                    return true;
                }
            }, defaults);
            if ($(element).attr('type').length > 0){
                options.type = $(element).attr('type');
            }
            // 当已经New过的对象，不再New第二次
            if ($(element).attr('init') == undefined || options.data){
                var selectorObj = new selectorClass(element, options);
                if (options.type == 'radio'){
                    selectorObj.radioBox();
                }
                if (options.type == 'dropdown'){
                    selectorObj.dropDown();
                }
                selectorObj = null;
            }
            $(element).attr('init', 1);
        });
    };

    /**
     * @description 初始化一个selectorfn对象,接收并初始化目标元素和配置信息 并解析Options数据成JSON对象
     * @param {String} element 页面选中的元素对象
     * @param {object} options 配置数组
     */
    var selectorClass = function (element, options) {
        this.element = $(element);
        if (this.element.attr('url')){ // 如果有URL从URL取数据
            $.ajax({
                type : 'GET',
                url : this.element.attr('url'),
                dataType : "json",
                async : false,
                plugin : true,
                success : function(resp){
                    options.data = resp;
                }
            });
            options.struct = 'list';
            options.dataLen = options.data.length;
        } else if (options.data == '') { // 没有传数据时 从HTML取
            options.data = $.parseJSON(this.element.html());
            options.struct = 'dict';
            var len = 0;
            $.each(options.data, function(i, k){
                len++;
            });
            options.dataLen = len;
        }
        if (options.data == null){
            options.data = {};
        }
        options.value = this.element.attr('value') ? this.element.attr('value') : options.value;
        options.value = options.value.split(','); // 变成List
        options.name = this.element.attr('name') ? this.element.attr('name') : options.name;
        options.required = this.element.attr('required') ? this.element.attr('required') : options.required;
        options.height = this.element.attr('height') ? this.element.attr('height') : options.height;
        options.width = this.element.attr('width') ? this.element.attr('width') : options.width;
        options.multi = this.element.attr('multi') ? this.element.attr('multi') : options.multi;
        options.view = this.element.attr('view') ? this.element.attr('view') : options.view;
        this.options = options;
        this.$popup = null;     //窗口对象
        this.$mask = null;      // 遮罩层
        this.element.html('');  // 目标里面的内容
    };
    
    /**
     * @description 扩展Checkbox和RadioBox的Html选择效果
     * @return {boolean} NULL
     */
    selectorClass.prototype.radioBox = function(){
        var __this = this, html = '';
        $.each(__this.options.data, function(_id, _name){
            var defclass =  _id == __this.options.value ? 'radio-checked' : '';
            var checked =   _id == __this.options.value ? 'checked = checked' : '';
            var item_html = [
                '<label class="radio '+defclass+'"><b></b>',
                    '<input type="radio" value="'+_id+'" ' + checked + ' name ="'+__this.element.attr('name')+'" /><span>'+_name+'</span>',
                '</label>',
           ];
            html += item_html.join("");
        });
        __this.element.html(html);
        // 绑定动作
        $(".radio > input").die('click').live("click", function(){
            $(this).parent().parent().find('.radio-checked').removeClass('radio-checked');
            if ($(this).attr('checked') == 'checked'){
                $(this).parent('.radio').addClass('radio-checked');
            }
            __this.options.callback({
                'value' : $(this).val(),
                'name' : $(this).attr('name')
            });
        });
    };
    
    /**
     * @description 扩展分组下拉选择器多选
     * @return {boolean} NULL
     */
    selectorClass.prototype.dropDown = function(){
        var __this = this, show_data = [], counts = 0;
        __this.element.addClass('dropdown');
        $.each(__this.options.data, function(i, item){
            if (__this.options.struct == 'list'){
                var id = item.id, name = item.name;
            } else {
                var id = i, name = item;
            }
            if (__this.options.multi){
                counts++;
                if ($.inArray(id, __this.options.value) >= 0){
                    show_data.push('<span style="background:#608908;">'+name +'</span>');
                }
            }else{
                if (__this.options.value == id){
                    show_data.push('<span style="background:#608908;">'+name +'</span>');
                }
            }
        });
        if (counts == __this.options.value.length && counts > 0){
            show_data = ['<span style="background:#608908;">全部</span>'];
        }
        if (show_data.length == 0){
            show_data.push('<span style="background:#608908;">'+__this.element.attr('title')+'</span>');
        }
        if (show_data.length >= 2){
            show_data = show_data.slice(0, 2);
            show_data.push("<span style='background:#608908;'>更多...</span>");
        }
        var html = [
            '<input type=hidden value="'+__this.options.value+'" name="'+__this.options.name+'" />',
            '<span class="show_data">'+show_data.join(" ")+'</span>',
        ];
        __this.element.html(html.join('')).die('click').live('click', function(event){
            if (__this.$popup == null){
                __this.show();
            }else{
                __this.close();
            }
            event.stopPropagation();
        });
        __this.options.callback({
            'name' : __this.options.name,
            'required' : __this.options.required,
            'data' : __this.options.data,
            'multi' : __this.options.multi,
            'struct' : __this.options.struct,
            'type' : __this.options.type,
            'value' : __this.element.find('input').val()
        });
    };
    
    /**
     * @description 显示下拉面板
     * @returns null
     */
    selectorClass.prototype.show = function( ){
        var __this = this;
        __this.$popup = __this.drawPopup();
        __this.$mask = $('<div class="mask"></div>').css({
            'height' : $(document).height(),
            'width' : $(document).width(),
            'position' : 'absolute', 'top' : 0, 'left': 0, 'z-index' : 999
        });
        if ($('.popup').length > 0){
            $('.popup').remove();
            $('.mask').remove();
        }
        $("body").append(__this.$popup).append(__this.$mask);
        __this.setPosition();
        __this.$popup.show();
        __this.bindAction();
    };
    
    /**
     * @description 关闭移除下拉面板
     * @returns null
     */
    selectorClass.prototype.close = function(){
        var __this = this;
        if (__this.$popup != null){
            __this.$popup.remove();
            __this.$mask.remove();
            __this.$popup = null;
            __this.$mask = null;
        }
        __this.options.callback({
            'name' : __this.options.name,
            'required' : __this.options.required,
            'data' : __this.options.data,
            'multi' : __this.options.multi,
            'struct' : __this.options.struct,
            'type' : __this.options.type,
            'value' : __this.element.find('input').val()
        });
    };
    
    /**
     * @description 下拉面板的内容
     * @returns HTML object
     */
    selectorClass.prototype.drawPopup = function(){
        var __this = this;
        var selected_value = __this.element.find("input:hidden").val().split(',');
        var content = [
           '<div class="popup">',
               '<div class="title" style="line-height: 25px;margin: 3px 10px;">',
                   '<h4>选择器</h4>',
                   '<input type="text" class="search" value="" style="float:right;padding:4px;" placeholder="输入查询关键字"/>',
               '</div>',
               '<hr class="mui-global-bt"/>',
               '<table><tr>',
                   '<td class="menus">'+__this.menuHtml()+'</td>',
                   '<td style="vertical-align:top;"><div class="content">'+__this.mainHtml()+'</div></td>',
               '</tr></table>'
        ];
        if (__this.options.multi && __this.options.view != 1){
            content.push('<hr class="mui-global-bt"/>');
            content.push('<div class="footer" style="background:#fafafa;height:25px;padding:3px 10px;text-align:right;">');
            content.push('<a class="mui-act ok" href="javascript:void(0);" >确定</a>');
            content.push('<a class="mui-act cancel" href="javascript:void(0);" >取消</a>');
            content.push('<a class="mui-act clears" href="javascript:void(0);" >清除</a>');
            content.push('</div>');
        }
        content.push('</div>');
        var $popupHtml = $(content.join('')), nums = {}, num = 0;
        // 设置默认选中的menus
        var flag = false;
        $.each(__this.options.data, function(i, item){
            if (__this.options.struct == 'list'){
                if ($.inArray(item.id, selected_value) >= 0){
                    if (nums.hasOwnProperty(item.type_id) == false){
                        nums[item.type_id] = 0;
                    } 
                    nums[item.type_id]++;
                    if (flag == false){
                        $popupHtml.find('.menus .tid_'+item.type_id).addClass('menus-selected');
                        flag = true;
                    }
                    $popupHtml.find('.menus .tid_'+item.type_id).find('.mui-global-num').html(nums[item.type_id]);
                    $popupHtml.find('.content > label').each(function(){
                        if ($(this).attr('tid') == item.type_id){
                            $(this).show();
                        }else{ $(this).hide();}
                    });
                }
            }else{
                if ($.inArray(i, selected_value) >= 0){
                    num++;
                    $popupHtml.find('.menus > .tid_0').addClass('menus-selected');
                    $popupHtml.find('.menus > .tid_0').find('.mui-global-num').html(num);
                }
            }
        });
        // 默认全选勾中
        $popupHtml.find('.menus > div').each(function(){
            var has_nums = $(this).find('.mui-global-num').html(), tid = $(this).find('input[type="checkbox"]').val();
            if (has_nums == $popupHtml.find('.content').find('label[tid="'+tid+'"]').length){
                $(this).find('input[type="checkbox"]').attr('checked', 'checked');
            }
        });
        return $popupHtml;
    };
    
    /**
     * @description 分组导行
     * @returns HTML
     */
    selectorClass.prototype.menuHtml = function(){
        var __this = this, html = [], menus = [];
        if (__this.options.struct == 'list'){
            $.each(__this.options.data, function(i, item){
                if (item.hasOwnProperty('type_id') == false){
                    item.type_id = -1;
                    item.type_name = '未知';
                }
                if (menus.hasOwnProperty(item.type_id) == false){
                    menus[item.type_id] = item.type_name;
                    html.push('<div class="tid_'+item.type_id+'">');
                    if (__this.options.multi){
                        html.push('<input type="checkbox" class="mcheckbox" value="'+item.type_id+'" />');
                    }
                    html.push('<span class="tname" style="color:blue;" tid="'+item.type_id+'">'+item.type_name+'</span>');
                    if (__this.options.multi){
                        html.push('<span class="mui-global-num red" style="padding-left:5px;"></span>');
                    }
                    html.push('</div>');
                }
            });
        }else{
            html.push('<div class="tid_0">');
            if (__this.options.multi && __this.options.view != 1){
                html.push('<input type="checkbox" class="mcheckbox" value="0" />');
            }
            html.push('<span class="tname" style="color:blue;" tid="0">全部</span>');
            if (__this.options.multi){
                html.push('<span class="mui-global-num red" style="padding-left:5px;"></span>');
            }
            html.push('</div>');
        }
        return html.join('');
    };
    
    /**
     * @description 分组数据列表内容
     * @returns HTML 
     */
    selectorClass.prototype.mainHtml = function(){
        var __this = this, html = [];
        var selected_value = __this.element.find("input:hidden").val().split(',');
        $.each(__this.options.data, function(i, item){
            if (__this.options.struct == 'list'){
                if (item.hasOwnProperty('type_id') == false){
                    item.type_id = -1;
                    item.type_name = '未知';
                }
                if ($.inArray(item.id, selected_value) >= 0){
                    html.push('<label class="selected" value="'+item.id+'" tid="'+item.type_id+'">'+item.name+'['+item.id+']'+'</label>');
                }else{
                    if (__this.options.view != 1){
                        html.push('<label value="'+item.id+'" tid="'+item.type_id+'">'+item.name+'['+item.id+']'+'</label>');
                    }
                }
            } else {
                if ($.inArray(i, selected_value) >= 0){
                    html.push('<label class="selected" value="'+i+'" tid="0">'+item+'['+i+']'+'</label>');
                }else{
                    if (__this.options.view != 1){
                        html.push('<label value="'+i+'" tid="0">'+item+'['+i+']'+'</label>');
                    }
                }
            }
        });
        return html.join('');
    };
    
    /**
     * @description 绑定动作
     * @returns null
     */
    selectorClass.prototype.bindAction = function(){
        var __this = this;
        if (__this.options.view != 1){ // view 模式时，没有此动作
            $('.menus > div').die('click').live('click', function(){
                var tid = $(this).find('.tname').attr('tid');
                $('.content').find('label').each(function(){
                    if ($(this).attr('tid') == tid){
                        $(this).show();
                    }else{ $(this).hide(); }
                });
                $(this).parent().find('.menus-selected').removeClass('menus-selected');
                $(this).addClass('menus-selected');
            });
        }
        
        // 查询框动作
        $('.title').find('.search').die("keyup").live("keyup", function(){
            var search_val = $(this).val().replace(/(^\s*)|(\s*$)/g, "");
            $(".content > label").each(function(){
               var cur_text = $(this).html();
               if (cur_text.indexOf(search_val) >= 0){ // 有查询到
                   $(this).show();
               }else{ $(this).hide();}
            });
        });
        
        if (__this.options.view != 1){ // view 模式时，没有此动作
            // 点击内容区的label时，处理动作
            $(".content").find("label").die("click").live("click", function(){
                if (__this.options.multi){ // 多选时
                    var tid = $(this).attr('tid');
                    var counts = parseInt($('.tid_'+tid).find('.mui-global-num').html());
                    counts = isNaN(counts) ? 0 : counts;
                    if ($(this).hasClass("selected")){
                        var nums = counts - 1;
                        $('.tid_'+tid).find('.mui-global-num').html(nums);
                        $(this).removeClass('selected');
                    }else{
                        var nums = counts + 1;
                        $('.tid_'+tid).find('.mui-global-num').html(nums);
                        $(this).addClass('selected');
                    }
                    if (parseInt(nums) == parseInt($('.content').find('label[tid="'+tid+'"]').length)){
                        $('.tid_'+tid).find('input[type="checkbox"]').attr('checked', 'checked');
                    }else{
                        $('.tid_'+tid).find('input[type="checkbox"]').removeAttr('checked');
                    }
                }else{ // 单选时
                    __this.element.find("input:hidden").val($(this).attr('value'));
                    __this.element.find(".show_data").html("<span style='background:#608908;'>"+$(this).html()+"</span>");
                    __this.close();
                }
                
            });
            
            // 全选CheckBox
            $('.menus').find('input[type="checkbox"]').die('click').live('click', function(){
                var checked = $(this).attr('checked'), tid = $(this).val(), content = $('.content').find('label[tid="'+tid+'"]');
                if (checked == 'checked'){
                    content.addClass('selected');
                    $(this).parent().find('.mui-global-num').html(content.length);
                }else{
                    content.removeClass('selected');
                    $(this).parent().find('.mui-global-num').html('');
                }
            });
        }
        
        // 点击确定时，设置值并关掉窗口
        $(".footer > .ok").die("click").live("click", function(){
            var val = [], name = [];
            $('.menus').find('input:checked').each(function(){
                name.push("<span style='background:#0074BB;'>"+$(this).next('.tname').text()+"</span>");
            });
            $(".popup").find("label.selected").each(function(){
                var tid = $(this).attr('tid');
                val.push($(this).attr('value'));
                if ($(".menus").find('.tid_'+tid).find('input[type="checkbox"]').attr('checked') != 'checked'){
                    name.push("<span style='background:#608908;'>"+$(this).html()+"</span>");
                }
            });
            __this.element.find("input:hidden").val(val.join(','));
            if (name.length >= 2){
                name = name.slice(0, 2);
                name.push("<span style='background:#608908;'>更多...</span>");
            }
            __this.element.find(".show_data").html(name.join('&nbsp;'));
            __this.close();
        });
        // 点击Mask层时 关掉弹出窗口
        $(".mask, .footer > .cancel").die('click').live('click', function(){
            __this.close();
        });
        // 清除按钮
        $('.footer > .clears').die('click').live('click', function(){
            $(".popup").find('.selected').removeClass('selected');
            $(".popup").find('.mui-global-num').html('');
            $(".popup").find('input:checked').removeAttr('checked');
        });
    };
    
    /**
     * 设置下拉框口的位置
     * @returns {Boolean}
     */
    selectorClass.prototype.setPosition = function(){
        var __this = this, left, top;
        var offset = __this.element.offset(),ele_width = __this.element.width();
        if (parseInt(offset.left) + parseInt(__this.options.width) > $(window).width()){
            left = parseInt(ele_width) + parseInt(offset.left) - parseInt(__this.options.width);
        }else{
            left = offset.left;
        }
        top = offset.top;
        //popup的样式
        __this.$popup.css({
            "top" : top + 30 + 'px',
            "left" : left + 'px',
            "zIndex" : 1000,
            "width" : __this.options.width + "px"
        }).find(".content").css({
            "height" : __this.options.height + 'px',
            "zoom":"1",
            "width":(__this.options.width - 110) + 'px',
            "overflow-y":"scroll",
            "padding": "5px"
        });
    };
})(jQuery);