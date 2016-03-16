<div class='error'><span class='awe-icon-exclamation-sign'></span><span class='tips'>错误信息</span></div>
<div class="mui-container mui-global-b">
    <div class="mui-sub-content">
        <form action="/admin/save" method="post" class='ajax'>
            <input type="hidden" value="<?php echo $data['input']['group_id']; ?>" name="id" />
            <input type="hidden" value="user_group" name="type" />
            <input type="hidden" value="1" name="owner_id" />
            <table class="mui-edit-table">
                <tr>
                    <th class="mui-table-row">账号组名称</th>
                    <td><input type="text" value="<?php echo $data['input']['group_name'];?>" name="group_name" class="input-text" maxLength="64" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">描述</th>
                    <td><textarea rows="5" cols="20" class="mui-global-b" name="group_desc" ><?php echo $data['input']['group_desc'];?></textarea></td>
                </tr>
                <tr>
                    <th class="mui-table-row"></th>
                    <td><button type="submit" class="btn">提交</button></td>
                </tr>
            </table>
        </form>
    </div>
</div>