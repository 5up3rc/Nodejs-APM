<div class="mui-filter mui-global-bb mui-global-bt" sidebar="/admin/menus">
    <div class="mui-filter-title">
        <h3>功能列表</h3>
        <a class='mui-act ajax priv' ajaxTarget='#aDialog' href="/admin/menu_modify?method=add" title="添加功能">+添加</a>
    </div>
    <hr class="mui-global-bb" style="margin-top:10px;" />
    <form action="/admin/menus" method="get" class="ajax search" ajaxTarget='#right-cont'>
    <div class='mui-filter-group'>
        <input type="text" class="input-text" name="menu_name" value="<?php echo $data['params']['menu_name']; ?>" style="min-width:100px;" placeholder="输入菜单名" />
        <div class="selectors" value='<?php echo $data['params']['module_id']; ?>' name="module_id" type="dropdown">
            <?php echo json_encode($data['module_list']);?>
        </div>
        <button type="submit" class="btn">查询</button>
    </div>
    </form>
</div>
<div class="mui-container mui-global-b m20">
    <table class="mui-data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>名称</th>
                <th>URL</th>
                <th>所属模块</th>
                <th>左测显示</th>
                <th>在线</th>
                <th>描述</th>
                <th style='width: 80px;'>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data['menu_list'] as $key => $value ){ ?>
            <tr>
                <td><?php echo $value['menu_id']; ?></td>
                <td><?php echo $value['menu_name']; ?></td>
                <td><?php echo $value['menu_url']; ?></td>
                <td><?php echo $value['module_name']; ?></td>
                <td><?php echo $data['status'][$value['is_show']]; ?></td>
                <td><?php echo $data['status'][$value['menu_online']]; ?></td>
                <td><?php echo $value['menu_desc']; ?></td>
                <td>
                    <a class='ajax priv' ajaxTarget='#aDialog' href="/admin/menu_modify?method=modify&menu_id=<?php echo $value['menu_id'];?>" title="编辑">编辑</a>
                    <a class='ajaxConfirm priv' href="/admin/del?type=menu_url&method=delete&id=<?php echo $value['menu_id'];?>" title="删除">删除</a>
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
    Ifox.ajaxTable("/admin/menus", params);
});
</script>