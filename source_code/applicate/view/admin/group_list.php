<div class="mui-filter mui-global-bb mui-global-bt" sidebar="/admin/groups">
    <div class="mui-filter-title">
        <h3>帐号组列表</h3>
        <a class='mui-act ajax priv' ajaxTarget='#aDialog' href="/admin/group_modify?method=add" title="添加用户组">+添加</a>
    </div>
</div>
<div class="mui-container mui-global-b m20">
    <table class="mui-data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>账号组名</th>
                <th>所有者</th>
                <th>描述</th>
                <th style='width:120px;'>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data['group_list'] as $key => $value ){ ?>
            <tr>
                <td><?php echo $value['group_id']; ?></td>
                <td><?php echo $value['group_name']; ?></td>
                <td><?php echo $data['user_list'][$value['owner_id']]; ?></td>
                <td><?php echo $value['group_desc']; ?></td>
                <td>
                <?php if ($value['group_id'] != 1){?>
                    <a class='ajax priv' ajaxTarget='#aDialog' href="/admin/group_modify?method=modify&group_id=<?php echo $value['group_id'];?>" title="编辑">编辑</a>
                    <a class='ajax priv' ajaxTarget='#aDialog' href="/admin/group_role?group_id=<?php echo $value['group_id'];?>" title="添加权限">添加权限</a>
                    <a class='ajaxConfirm priv' href="/admin/del?type=user_group&method=delete&id=<?php echo $value['group_id'];?>" title="删除">删除</a>
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
    Ifox.ajaxTable("/admin/groups", params);
});
</script>