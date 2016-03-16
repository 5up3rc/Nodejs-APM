<div class='error'><span class='awe-icon-exclamation-sign'></span><span class='tips'>错误信息</span></div>
<div class="mui-container mui-global-b">
    <div class="mui-sub-content">
        <form action="/admin/save" method="post" class='ajax'>
            <input type="hidden" value="<?php echo $data['input']['module_id']; ?>" name="id" />
            <input type="hidden" value="module" name="type" />
            <input type="hidden" value="index.php" name="module_url" />
            <table class="mui-edit-table">
                <tr>
                    <th class="mui-table-row">模块名称</th>
                    <td><input type="text" value="<?php echo $data['input']['module_name'];?>" name="module_name" class="input-text" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">模块排序数字</th>
                    <td><input type="number" value="<?php echo $data['input']['module_sort'];?>" name="module_sort" class="input-text" /><span class="silver">(数字越小越靠前)</span></td>
                </tr>
                <tr>
                    <th class="mui-table-row">是否在线</th>
                    <td><div class="selectors" value='<?php echo $data['input']['module_online']; ?>' name="module_online" type='radio'>
                        <?php echo json_encode ( array( 1 => '在线', 0 => '下线' ) ); ?>
                    </div></td>
                </tr>
                <tr>
                    <th class="mui-table-row">描述</th>
                    <td><textarea rows="5" cols="20" class="mui-global-b" name="module_desc" ><?php echo $data['input']['module_desc'];?></textarea></td>
                </tr>
                <tr>
                    <th class="mui-table-row"></th>
                    <td><button type="submit" class="btn">提交</button></td>
                </tr>
            </table>
        </form>
    </div>
</div>
