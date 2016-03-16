<div class="mui-filter mui-global-bb mui-global-bt">
    <div class="mui-filter-title">
        <h3>错误详情</h3>
        <?PHP if ($data['search']['goback'] == 1){ ?>
            <a class='mui-act ajax' ajaxTarget='#right-cont' href="/monitor/error?cmd=<?PHP echo $data['search']['cmd']."&project_id=".$data['search']['project_id']."&request_date=".$data['search']['request_date'];?>" title="返回">返回</a>
        <?PHP } ?>
    </div>
    <hr class="mui-global-bb" style="margin-top:10px;" />
    <form action="/monitor/error_detail" method="get" class="ajax search" ajaxTarget='#right-cont'>
        <div class='mui-filter-group'>
            <input type="hidden" name = "request_date" value="<?php echo $data['search']['request_date'];?>" />
            <span id="datepicker"></span>&nbsp;
            <input type="text" class="input-text" name="cmd" value="<?php echo $data['search']['cmd']; ?>" style="min-width:100px;" placeholder="输入API名称" />
            <span class="selectors" value='<?php echo $data['search']['project_id']; ?>' name="project_id" type="dropdown" title="选择权限组">
                <?php echo json_encode($data['project']);?>
            </span>
            <button type="submit" class="btn">查询</button>
        </div>
    </form>
</div>
<div class="mui-container mui-global-b m20">
    <table class="mui-data-table">
        <thead>
        <tr>
            <th>Api名称</th>
            <th>主机IP</th>
            <th>代码文件</th>
            <th>代码所在行</th>
            <th>请求时间</th>
            <th>用户ID</th>
            <th>msg</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data['list'] as $key => $value ){ ?>
            <tr>
                <td><?php echo $value['cmd']; ?></td>
                <td><?php echo $value['hostip']; ?></td>
                <td><?php echo $value['srcfile']; ?></td>
                <td><?php echo $value['srcline']; ?></td>
                <td><?php echo date("Y-m-d H:i:s", $value['request_time']); ?></td>
                <td><?php echo $value['player_id']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <!--    --><?php //echo $data['pager']; ?>
</div>
<script type="text/javascript">
    $(function(){
        var datepicker = new pickerDateRange("datepicker", "date", {
            isSingleDay : true,
            shortOpr : true,
            startDate : "<?php echo $data['search']['request_date'];?>",
            monthRangeMax : 3,
            minValidDate : 1379174400,
            success : function (dateObj){
                $("input[name=request_date]").val(dateObj.startDate);
            }
        });
        var params = $.parseJSON('<?php echo json_encode($data['search'])?>');
        Ifox.ajaxTable("/admin/syslog", params);
    });
</script>