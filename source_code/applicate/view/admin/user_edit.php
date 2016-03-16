<div class='error'><span class='awe-icon-exclamation-sign'></span><span class='tips'>错误信息</span></div>
<div class="mui-container mui-global-b">
    <div class="mui-sub-content">
        <form action="/admin/save" method="post" class='ajax'>
            <input type="hidden" value="<?php echo $data['input']['user_id']; ?>" name="id" />
            <input type="hidden" value="user" name="type" />
            <table class="mui-edit-table">
                <tr>
                    <th class="mui-table-row">登录名</th>
                    <td><input type="text" value="<?php echo $data['input']['user_name'];?>" name="user_name" class="input-text" maxLength="64" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">密码</th>
                    <td><input type="password" value="" name="password" maxLength="32" class="input-text" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">姓名</th>
                    <td><input type="text" value="<?php echo $data['input']['real_name'];?>" name="real_name" class="input-text" maxLength="64" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">手机</th>
                    <td><input type="tel" value="<?php echo $data['input']['mobile'];?>" name="mobile" class="input-text" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">邮件</th>
                    <td><input type="email" value="<?php echo $data['input']['email'];?>" name="email" class="input-text" autofocus="true" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">描述</th>
                    <td><textarea rows="5" cols="20" class="mui-global-b" name="user_desc" ><?php echo $data['input']['user_desc'];?></textarea></td>
                </tr>
                <?php if ($data['sess']['user_name'] == 'admin') {?>
                <tr>
                    <th class="mui-table-row">账号组</th>
                    <td><div class="selectors" value='<?php echo $data['input']['user_group']; ?>' name="user_group" type='dropdown' title='选择帐号组'>
                        <?php echo json_encode($data['group_list']);?>
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