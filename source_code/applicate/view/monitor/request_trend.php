<div class="mui-filter mui-global-bb mui-global-bt">
    <div class="mui-filter-title">
        <h3>10分钟API请求总量走势</h3>
        <?PHP if ($data['search']['goback'] == 1){ ?>
        <a class='mui-act ajax' ajaxTarget='#right-cont' href="/monitor/access?cmd=<?PHP echo $data['search']['cmd']."&project_id=".$data['search']['project_id']."&request_date=".$data['search']['request_date'];?>" title="返回">返回</a>
        <?PHP } ?>
    </div>
    <hr class="mui-global-bb" style="margin-top:10px;" />
    <form action="/monitor/request_trend" method="get" class="ajax search" ajaxTarget='#right-cont'>
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
    <div id='data_charts' style="padding:30px;"></div>
</div>
<script type="text/javascript">
    $(function(){
        var result = $.parseJSON('<?php echo json_encode($data['day']);?>');
        var l_result = $.parseJSON('<?php echo json_encode($data['l_day']);?>');
        var ls_result = $.parseJSON('<?php echo json_encode($data['ls_day']);?>');
        var date = <?php echo json_encode($data['date']);?>;
        var l_date = <?php echo json_encode($data['l_date']);?>;
        var ls_date = <?php echo json_encode($data['ls_date']);?>;
        var ydata = [], categories = [], series = [];
        $.each(result, function(i, value){
            var dateObj = new Date(value.request_minute*1000);
            var cate = dateObj.getHours()+":"+dateObj.getMinutes();
            categories.push( cate );
            ydata.push({ name : cate, y : parseInt(value.request_count) });
        });
        series.push({name : date, data : ydata });
        ydata = [];
        $.each(l_result, function(i, value){
            var dateObj = new Date(value.request_minute*1000);
            var cate = dateObj.getHours()+":"+dateObj.getMinutes();
            ydata.push({ name : cate, y : parseInt(value.request_count) });
        });
        series.push({name : l_date, data : ydata });
        ydata = [];
        $.each(ls_result, function(i, value){
            var dateObj = new Date(value.request_minute*1000);
            var cate = dateObj.getHours()+":"+dateObj.getMinutes();
            ydata.push({ name : cate, y : parseInt(value.request_count) });
        });
        series.push({name : ls_date, data : ydata });
        $("#data_charts").empty().createChart({
            chartType: 'line',
            categories: categories,
            series : series
        });

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
    });
</script>