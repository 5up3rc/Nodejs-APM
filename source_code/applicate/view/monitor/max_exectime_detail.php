<div class="mui-filter mui-global-bb mui-global-bt">
    <div class="mui-filter-title">
        <h3>最大耗时详情</h3>
        <?PHP if ($data['search']['goback'] == 1){ ?>
        <a class='mui-act ajax' ajaxTarget='#right-cont' href="/monitor/access?cmd=<?PHP echo $data['search']['cmd']."&project_id=".$data['search']['project_id']."&request_date=".$data['search']['request_date'];?>" title="返回">返回</a>
        <?PHP } ?>
    </div>
</div>
<div class="mui-container mui-global-b m20">
    <table class="mui-data-table">
        <thead>
        <tr>
            <th>Api名称</th>
            <th>主机ip</th>
            <th>请求耗时（毫秒）</th>
            <th>请求时间</th>
            <th>msg</th>
            <th>玩家id</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data['return'] as $key => $value ){ ?>
            <tr>
                <td><?php echo $value['cmd']; ?></td>
                <td><?php echo $value['hostip']; ?></td>
                <td><?php echo $value['exectime']; ?></td>
                <td><?php echo date("Y-m-d H:i:s", $value['request_time']); ?></td>
                <td><?php echo $value['msg']; ?></td>
                <td><?PHP echo $value['playerid'];?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>