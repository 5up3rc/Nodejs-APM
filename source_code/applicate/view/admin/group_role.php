<div class='error'><span class='awe-icon-exclamation-sign'></span><span class='tips'>错误信息</span></div>
<div class="mui-container mui-global-b">
    <div class="mui-sub-content">
        <form action="/admin/save" method="post" class='ajax'>
            <input type="hidden" value="<?php echo $data['group_role']['group_id']; ?>" name="id" />
            <input type="hidden" value="user_group" name="type" />
            <table class="mui-edit-table">
                <?php foreach ($data['menu_list'] as $module_name => $menu_list ){ ?>
                <tr>
                    <th class="mui-table-row"><?php echo $module_name; ?></th>
                    <td><div class="selectors" value='<?php echo implode(',', $data['value'][$module_name]); ?>' name="group_role[]" type="dropdown" multi=1 title="选择权限模块">
                    <?php echo json_encode($menu_list);?>
                    </div></td>
                </tr>
                <?php } ?>
                <tr>
                    <th class="mui-table-row"></th>
                    <td><button type="submit" class="btn">提交</button></td>
                </tr>
            </table>
        </form>
    </div>
</div>
