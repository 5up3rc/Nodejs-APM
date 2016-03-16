<div class="mui-filter mui-global-bb mui-global-bt" sidebar="/admin/users">
    <div class="mui-filter-title">
        <h3>用户列表</h3>
        <a class='mui-act ajax priv' ajaxTarget='#aDialog' href="/admin/modify?method=add" title="添加用户">+添加</a>
    </div>
    <hr class="mui-global-bb" style="margin-top:10px;" />
    <form action="/admin/users" method="get" class="ajax search" ajaxTarget='#right-cont'>
    <div class='mui-filter-group'>
        <input type="text" class="input-text" name="user_name" value="<?php echo $data['params']['user_name']; ?>" style="min-width:100px;" placeholder="输入用户名" />
        <span class="selectors" value='<?php echo $data['params']['user_group']; ?>' name="user_group" type="dropdown" title="选择权限组">
            <?php echo json_encode($data['group_list']);?>
        </span>
        <button type="submit" class="btn">查询</button>
    </div>
    </form>
</div>
<div class="mui-container mui-global-b m20">
    <table class="mui-data-table">
        <thead>
            <tr>
                <th style="width:60px">#</th>
                <th>登录名</th>
                <th>姓名</th>
                <th>手机</th>
                <th style="width:140px">邮箱</th>
                <th style="width:140px">登录时间</th>
                <th>登录IP</th>
                <th>是否可用</th>
                <th>Group#</th>
                <th>描述</th>
                <th style="width:150px">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data['user_list'] as $key => $value ){ ?>
            <tr>
                <td><?php echo $value['user_id']; ?></td>
                <td><?php echo $value['user_name']; ?></td>
                <td><?php echo $value['real_name']; ?></td>
                <td><?php echo $value['mobile']; ?></td>
                <td><?php echo $value['email']; ?></td>
                <td><?php echo date("Y-m-d H:i:s", $value['login_time']); ?></td>
                <td><?php echo $value['login_ip']; ?></td>
                <td><?php echo $data['user_status'][$value['status']]; ?></td>
                <td><?php echo $data['group_list'][$value['user_group']]; ?></td>
                <td><?php echo $value['user_desc']; ?></td>
                <td>
                <?php if ($value['user_id'] != 1){ ?>
                    <a class='ajax priv' ajaxTarget='#aDialog' href="/admin/modify?method=modify&user_id=<?php echo $value['user_id'];?>" title="编辑<?php echo $value['user_name']; ?>">编辑</a>
                <?php if ($value['status'] == 1){?>
                    <a class='ajaxConfirm priv' href="/admin/stop?method=stop&user_id=<?php echo $value['user_id'];?>&status=0" title="停封">禁用</a>
                <?php }else{?>
                    <a class='ajaxConfirm priv' href="/admin/stop?method=stop&user_id=<?php echo $value['user_id'];?>&status=1" title="启用">启用</a>
                <?php } ?>
                    <a class='ajaxConfirm priv' href="/admin/del?type=user&method=delete&id=<?php echo $value['user_id'];?>" title="删除">删除</a>
                <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<!--    --><?php //echo $data['pager']; ?>
</div>
<script type="text/javascript">
$(function(){
    var params = $.parseJSON('<?php echo json_encode($data['params'])?>');
    Ifox.ajaxTable("/admin/users", params);
});
</script>