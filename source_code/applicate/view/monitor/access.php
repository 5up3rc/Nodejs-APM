<div class="mui-filter mui-global-bb mui-global-bt">
    <div class="mui-filter-title">
        <h3>流水监控</h3>
    </div>
    <hr class="mui-global-bb" style="margin-top:10px;" />
    <form action="/monitor/access" method="get" class="ajax search" ajaxTarget='#right-cont'>
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
                <th>搜索范围内总访问量（次）</th>
                <th>平均每次请求耗时（毫秒）</th>
                <th>最长耗时（毫秒）</th>
                <th>最小耗时（毫秒）</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data['list'] as $key => $value ){ ?>
            <tr>
                <td><?php echo $value['cmd']; ?></td>
                <td><?php echo $value['counts']; ?></td>
                <td><?php echo $value['avg']; ?></td>
                <td><?php echo $value['max']; ?></td>
                <td><?php echo $value['min']; ?></td>
                <td><?PHP
                echo "<a class='ajax' ajaxTarget='#right-cont' href='/monitor/request_trend?goback=1&cmd={$value['cmd']}&request_date={$value['request_date']}&project_id={$value['project_type']}'>API每10分钟请求总量</a> | ";
                echo "<a class='ajax' ajaxTarget='#right-cont' href='/monitor/max_exectime_detail?goback=1&cmd={$value['cmd']}&request_date={$value['request_date']}&project_id={$value['project_type']}'>最大耗时详情</a>";
                ?></td>
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