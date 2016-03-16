<div class='error'><span class='awe-icon-exclamation-sign'></span><span class='tips'>错误信息</span></div>
<div class="mui-container mui-global-b">
    <div class="mui-sub-content">
        <form action="/admin/save" method="post" class='ajax'>
            <input type="hidden" value="<?php echo $data['input']['navigate_id']; ?>" name="id" />
            <input type="hidden" value="navigate" name="type" />
            <table class="mui-edit-table">
                <tr>
                    <th class="mui-table-row">名称</th>
                    <td><input type="text" value="<?php echo $data['input']['navigate_name'];?>" name="navigate_name" class="input-text" maxLength="64" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">链接</th>
                    <td><input type="text" value="<?php echo $data['input']['navigate_url'];?>" name="navigate_url" class="input-text" maxLength="64" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">ICON</th>
                    <td><input type="text" value="<?php echo $data['input']['navigate_icon'];?>" name="navigate_icon" class="input-text" maxLength="64" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">排序号</th>
                    <td><input type="number" value="<?php echo $data['input']['navigate_sort'];?>" name="navigate_sort" class="input-text" maxLength="64" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">所属模块</th>
                    <td><div class="selectors" value='<?php echo $data['input']['module_id']; ?>' name="module_id" type='dropdown' multi=1 title='选择模块'>
                        <?php echo json_encode($data['module_list']);?>
                    </div></td>
                </tr>
                <tr>
                    <th class="mui-table-row">是否在线</th>
                    <td><div class="selectors" value='<?php echo $data['input']['navigate_online']; ?>' name="navigate_online" type='radio'>
                        <?php echo json_encode(array(0 => '否', 1 => '是'));?>
                    </div></td>
                </tr>
                <tr>
                    <th class="mui-table-row">描述</th>
                    <td><textarea rows="5" cols="20" class="mui-global-b" name="navigate_desc" ><?php echo $data['input']['navigate_desc'];?></textarea></td>
                </tr>
                <tr>
                    <th class="mui-table-row"></th>
                    <td><button type="submit" class="btn">提交</button></td>
                </tr>
            </table>
        </form>
    </div>
</div>
