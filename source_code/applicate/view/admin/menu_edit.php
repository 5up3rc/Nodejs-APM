<div class='error'><span class='awe-icon-exclamation-sign'></span><span class='tips'>错误信息</span></div>
<div class="mui-container mui-global-b">
    <div class="mui-sub-content">
        <form action="/admin/save" method="post" class='ajax'>
            <input type="hidden" value="<?php echo $data['input']['menu_id']; ?>" name="id" />
            <input type="hidden" value="menu_url" name="type" />
            <table class="mui-edit-table">
                <tr>
                    <th class="mui-table-row">名称</th>
                    <td><input type="text" value="<?php echo $data['input']['menu_name'];?>" name="menu_name" class="input-text" maxLength="64" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">链接</th>
                    <td><input type="text" value="<?php echo $data['input']['menu_url'];?>" name="menu_url" class="input-text" maxLength="64" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">所属模块</th>
                    <td><div class="selectors" value='<?php echo $data['input']['module_id']; ?>' name="module_id" type='dropdown' title='选择模块'>
                        <?php echo json_encode($data['module_list']);?>
                    </div></td>
                </tr>
                <tr>
                    <th class="mui-table-row">左侧显示</th>
                    <td><div class="selectors" value='<?php echo $data['input']['is_show']; ?>' name="is_show" type='radio'>
                        <?php echo json_encode(array(0 => '否', 1 => '是'));?>
                    </div></td>
                </tr>
                <tr>
                    <th class="mui-table-row">是否在线</th>
                    <td><div class="selectors" value='<?php echo $data['input']['menu_online']; ?>' name="menu_online" type='radio'>
                        <?php echo json_encode(array(0 => '否', 1 => '是'));?>
                    </div></td>
                </tr>
                <tr>
                    <th class="mui-table-row">描述</th>
                    <td><textarea rows="5" cols="20" class="mui-global-b" name="menu_desc" ><?php echo $data['input']['menu_desc'];?></textarea></td>
                </tr>
                <tr>
                    <th class="mui-table-row"></th>
                    <td><button type="submit" class="btn">提交</button></td>
                </tr>
            </table>
        </form>
    </div>
</div>