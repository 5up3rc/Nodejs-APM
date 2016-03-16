<div class="mui-filter mui-global-bb mui-global-bt" sidebar="/admin/modules">
    <div class="mui-filter-title">
        <h3>菜单模块</h3>
        <a class='mui-act ajax priv' ajaxTarget='#aDialog' href="/admin/module_modify?method=add" title='添加菜单模块'>+添加</a>
    </div>
</div>
<div class="mui-container mui-global-b m20">
    <table class="mui-data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>模块名</th>
                <th>排序</th>
                <th>是否在线</th>
                <th>描述</th>
                <th style='width:80px'>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data['group_list'] as $key => $value ){ ?>
            <tr>
                <td><?php echo $value['module_id']; ?></td>
                <td><?php echo $value['module_name']; ?></td>
                <td><?php echo $value['module_sort']; ?></td>
                <td><?php echo $data['status'][$value['module_online']]; ?></td>
                <td><?php echo $value['module_desc']; ?></td>
                <td>
                    <a class='ajax priv' ajaxTarget='#aDialog' href="/admin/module_modify?method=modify&module_id=<?php echo $value['module_id'];?>" title="编辑">编辑</a>
                    <a class='ajaxConfirm priv' href="/admin/del?type=module&method=delete&id=<?php echo $value['module_id'];?>" title="删除">删除</a>
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
    Ifox.ajaxTable("/admin/modules", params);
});
</script>