<div class="mui-filter mui-global-bb mui-global-bt">
    <div class="mui-filter-title">
        <h3>操作日志</h3>
    </div>
</div>
<div class="mui-container mui-global-b m20">
    <table class="mui-data-table">
        <thead>
            <tr>
                <th width="50px">#</th>
                <th width="50px">操作员</th>
                <th width="80px">行为</th>
                <th width="80px">类型</th>
                <th width="120px">操作时间</th>
                <th>操作结果</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data['syslog_list'] as $key => $value ){ ?>
            <tr>
                <td><?php echo $value['op_id']; ?></td>
                <td><?php echo $value['user_name']; ?></td>
                <td><?php echo $value['action']; ?></td>
                <td><?php echo $value['class_name']; ?></td>
                <td><?php echo date('Y-m-d H:i:s', $value['op_time']); ?></td>
                <td style="word-break : break-all; word-wrap:break-word"><?php print_r(json_decode(urldecode($value['result']), true)); ?></td>
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