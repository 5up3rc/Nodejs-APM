<div class="mui-filter mui-global-bb mui-global-bt" sidebar="/admin/navigate">
    <div class="mui-filter-title">
        <h3>导行列表</h3>
        <a class='mui-act ajax priv' ajaxTarget='#aDialog' href="/admin/navigate_modify?method=add" title="添加导航">+添加</a>
    </div>
    <form action="/admin/navigate" method="get" class="ajax search" ajaxTarget='#right-cont'>
    <div class='mui-filter-group'>
        <input type="text" class="input-text" name="navigate_name" value="<?php echo $data['params']['navigate_name']; ?>" style="min-width:100px;" placeholder="输入导行名" />
        <div class="selectors" value='<?php echo $data['params']['module_id']; ?>' name="module_id" type='dropdown'>
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
                <th>导行名称</th>
                <th>导行URL</th>
                <th>排序号</th>
                <th>导行ICON</th>
                <th>模块</th>
                <th>导行描述</th>
                <th>是否在线</th>
                <th style='width: 80px'>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($data['list'] as $key => $value ){
                $module = explode(',', $value['module_id']);
                $module_name = array();
                foreach ($module as $id){
                    $module_name[] = isset($data['module_list'][$id]) ? $data['module_list'][$id]: '';
                }
        ?>
            <tr>
                <td><?php echo $value['navigate_id']; ?></td>
                <td><?php echo $value['navigate_name']; ?></td>
                <td><?php echo $value['navigate_url']; ?></td>
                <td><?php echo $value['navigate_sort']; ?></td>
                <td><i class="<?php echo $value['navigate_icon']; ?>"></i></td>
                <td><?php echo implode(', ', $module_name); ?></td>
                <td><?php echo $value['navigate_desc']; ?></td>
                <td><?php echo $data['status'][$value['navigate_online']]; ?></td>
                <td>
                    <a class='ajax priv' ajaxTarget='#aDialog' href="/admin/navigate_modify?method=modify&navigate_id=<?php echo $value['navigate_id'];?>" title="编辑">编辑</a>
                    <a class='ajaxConfirm priv' href="/admin/del?type=navigate&method=delete&id=<?php echo $value['navigate_id'];?>" title="删除">删除</a>
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
    Ifox.ajaxTable("/admin/navigate", params);
});
</script>