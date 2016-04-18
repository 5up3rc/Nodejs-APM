<div class="mui-filter mui-global-bb mui-global-bt">
    <div class="mui-filter-title">
        <h3>项目列表</h3>
        <a class='mui-act ajax priv' ajaxTarget='#aDialog' href="/monitor/modify" title="添加项目">+添加</a>
    </div>
</div>
<div class="mui-container mui-global-b m20">
    <table class="mui-data-table">
        <thead>
            <tr>
                <th>项目名称</th>
                <th>项目标识</th>
                <th>Access上报比例</th>
                <th>所属部门</th>
                <th>负责人</th>
                <th>联系电话</th>
                <th>描述</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data['list'] as $key => $value ){ ?>
            <tr>
                <td><?php echo $value['name']; ?></td>
                <td><?php echo $value['id']; ?></td>
                <td><?php echo $value['ratio']; ?></td>
                <td><?php echo $value['dept']; ?></td>
                <td><?php echo $value['leader']; ?></td>
                <td><?php echo $value['tel']; ?></td>
                <td><?php echo $value['desc']; ?></td>
                <td>
                    <a class='ajax priv' ajaxTarget='#aDialog' href="/monitor/modify?id=<?php echo $value['id'];?>" title="编辑项目">编辑</a>
                    <a class='ajaxConfirm priv' href="/monitor/remove?id=<?php echo $value['id'];?>" title="删除项目">删除</a>
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
    Ifox.ajaxTable("/admin/syslog", params);
});
</script>