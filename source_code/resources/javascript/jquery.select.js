$(function(){
    $.fn.applySelect = function(){
        return this.each(function(){
            if ($('body .select-mask').size() == 0) {
                $('body').append('<div class="select-mask"></div>');
            }
            var mask = $('body .select-mask');
            var data = $(this).data('data');
            if (data == null) {
                var str = $(this).attr('data');
                if (str == null) {
                    str = $(this).text();
                }
                eval("var data = " + str);
                $(this).data('data', data);
                $(this).html('');
            }
            var hasGroup = $(this).hasClass('select-with-group');
            var name = $(this).attr('name');
            var val = $(this).attr('value') || '';
            var arr = val.split(',');
            var valArr = {};
            for (var i in arr) {
                if (arr[i] == '') {
                    continue;
                }
                valArr[arr[i]] = 1;
            }
            var title = $(this).attr('title') || '--请选择--';
            var html = '';
            html += '<div class="top">';
            html += '<div class="text"></div><span class="icon"></span>';
            html += '<input type="hidden" name="' + name + '" value="' + val + '" />';
            html += '</div>';
            var select = $(this);
            var showPopup = function() {
                mask.show();
                $('.select .popup').slideUp('fast');
                select.find('.popup').slideDown('fast');
            }
            var hidePopup = function(flag) {
                mask.hide();
                $('.select .popup').slideUp('fast');
                if (flag && typeof(window.selectCallback) == 'function') {
                    var data = {
                        name: name,
                        value: select.find('input[name]').val(),
                    };
                    window.selectCallback(data);
                }
            }
            mask.die('click').live('click', function(e){
                hidePopup(true);
            });
            if (!hasGroup) { // 不带组选择面板
                html += '<div class="popup">';
                html += '<ul class="option">';
                html += '<li val=""><div class="text">' + title + '</div><span class="icon"></span></li>';
                for (var k in data) {
                    html += '<li val="' + k + '"';
                    if (valArr[k]) {
                        html += ' class="selected"';
                    }
                    html += '><div class="text">' + data[k] + '</div><span class="icon"></span></li>';
                }
                html += '</ul>';
                html += '</div>';
                $(this).html(html);
                var text = $(this).find('.top .text');
                var input = $(this).find('input[name]');
                var option = $(this).find('.option');
                var popup = $(this).find('.popup');
                var top = $(this).find('.top');
                var optionLi = $(this).find('.option li');
                var updateSelectedVal = function() {
                    var arr1 = [], arr2 = [];
                    optionLi.each(function(){
                        if ($(this).hasClass('selected')) {
                            arr1.push($(this).attr('val'));
                            arr2.push($(this).text());
                        }
                    });
                    if (arr1.length > 0) {
                        input.val(arr1.join(','));
                        text.html(arr2.join(','));
                    } else {
                        input.val('');
                        text.html(title);
                    }
                }
                optionLi.click(function(){
                    optionLi.removeClass('selected');
                    $(this).addClass('selected');
                    updateSelectedVal();
                    hidePopup(true);
                });
                updateSelectedVal();
                top.click(function(){
                    if (popup.css('display') == 'none') {
                        showPopup();
                    } else {
                        hidePopup(false);
                    }
                });
            } else { // 带组选择面板
                html += '<div class="popup">';
                html += '<div class="search"><input type="text" /><span class="icon"></span></div>';
                html += '<ul class="group">';
                var groups = {};
                for (var k in data) {
                    var obj = data[k];
                    groups[obj.group] = 1;
                }
                for (var grp in groups) {
                    html += '<li val="' + grp + '"';
                    if (k == val) {
                        html += ' class="selected"';
                    }
                    html += '><span class="icon icon-left"></span><div class="text">' + grp + '</div><span class="icon icon-right"></span></li>';
                }
                html += '</ul>';
                html += '<div class="button"><span class="cancel">取消</span><span class="confirm">确定</span></div>';
                html += '<ul class="option">';
                for (var k in data) {
                    var obj = data[k];
                    var val = obj.val;
                    var txt = obj.text;
                    var grp = obj.group;
                    html += '<li val="' + val + '" group="' + grp + '"';
                    if (valArr[val]) {
                        html += ' class="selected"';
                    }
                    html += '><div class="text">' + txt + '</div><span class="icon"></span></li>';
                }
                html += '</ul>';
                html += '</div>';
                $(this).html(html);
                var text = $(this).find('.top .text');
                var input = $(this).find('input[name]');
                var popup = $(this).find('.popup');
                var top = $(this).find('.top');
                var groupLi = $(this).find('.group li');
                var option = $(this).find('.option');
                if ($(this).position().left + $(this).outerWidth() + option.outerWidth() > $(window).outerWidth()) {
                    option.css({left: -180});
                } else {
                    option.css({left: 176});
                }
                var search = $(this).find('.search input');
                groupLi.click(function(){
                    groupLi.removeClass('selected');
                    $(this).addClass('selected');
                    var group = $(this).attr('val');
                    var filter = search.val();
                    option.find('li').each(function(){
                        if ($(this).attr('group') == group && (filter == '' || $(this).text().indexOf(filter) != -1)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                    option.show();
                });
                search.keyup(function(){
                    groupLi.filter('.selected').trigger('click');
                });
                var optionLi = $(this).find('.option li');
                var updateSelectedVal = function() {
                    var arr1 = [], arr2 = [];
                    optionLi.each(function(){
                        if ($(this).hasClass('selected')) {
                            arr1.push($(this).attr('val'));
                            arr2.push($(this).text());
                        }
                    });
                    if (arr1.length > 0) {
                        input.val(arr1.join(','));
                        text.html(arr2.join(','));
                    } else {
                        input.val('');
                        text.html(title);
                    }
                }
                updateSelectedVal();
                var inputBackup = input.val();
                var textBackup = text.html();
                optionLi.click(function(){
                    $(this).toggleClass('selected');
                    updateSelectedVal();
                });
                top.click(function(){
                    if (popup.css('display') == 'none') {
                        inputBackup = input.val();
                        textBackup = text.html();
                        showPopup();
                    } else {
                        hidePopup(false);
                    }
                });
                $(this).find('.button .cancel').click(function(){
                    text.html(textBackup);
                    input.val(inputBackup);
                    var arr = inputBackup.split(',');
                    var valArr = {};
                    for (var i in arr) {
                        if (arr[i] == '') {
                            continue;
                        }
                        valArr[arr[i]] = 1;
                    }
                    optionLi.each(function(){
                        if (valArr[$(this).attr('val')]) {
                            $(this).addClass('selected');
                        } else {
                            $(this).removeClass('selected');
                        }
                    });
                    updateSelectedVal();
                    hidePopup(false);
                });
                $(this).find('.button .confirm').click(function(){
                    hidePopup(true);
                });
            }
        });
    };
});
