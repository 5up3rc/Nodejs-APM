<div class="mui-filter mui-global-bb mui-global-bt">
    <div class="mui-filter-title">
        <h3>WEB机流水分布</h3>
    </div>
    <hr class="mui-global-bb" style="margin-top:10px;" />
    <form action="/monitor/host_access" method="get" class="ajax search" ajaxTarget='#right-cont'>
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

        var result = $.parseJSON('<?php echo json_encode($data['list']);?>');
        var ydata = [], categories = [];
        $.each(result, function(i, value){
            categories.push( value.hostip );
            ydata.push({ name : value.hostip, y : parseInt(value.counts) });
        });
        $("#data_charts").empty().createChart({
            chartType: 'pie',
            categories: categories,
            series : {name : '各WEB机请求分布', data : ydata }
        });
    });
</script>