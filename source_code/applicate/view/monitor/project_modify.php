<div class='error'><span class='awe-icon-exclamation-sign'></span><span class='tips'>错误信息</span></div>
<div class="mui-container mui-global-b">
    <div class="mui-sub-content">
        <form action="/monitor/save" method="post" class='ajax'>
            <input type="hidden" value="<?php echo $data['id']; ?>" name="id" />
            <table class="mui-edit-table">
                <tr>
                    <th class="mui-table-row">项目名称</th>
                    <td><input type="text" value="<?php echo $data['name'];?>" name="name" class="input-text" maxLength="64" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">所属部门</th>
                    <td><input type="text" value="<?php echo $data['dept'];?>" name="dept" class="input-text" maxLength="64" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">负责人</th>
                    <td><input type="text" value="<?php echo $data['leader'];?>" name="leader" class="input-text" maxLength="64" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">联系电话</th>
                    <td><input type="text" value="<?php echo $data['tel'];?>" name="tel" class="input-text" maxLength="64" required="true" /></td>
                </tr>
                <tr>
                    <th class="mui-table-row">描述</th>
                    <td><textarea rows="5" cols="20" class="mui-global-b" name="desc" ><?php echo $data['desc'];?></textarea></td>
                </tr>
                <tr>
                    <th class="mui-table-row"></th>
                    <td><button type="submit" class="btn">提交</button></td>
                </tr>
            </table>
        </form>
    </div>
</div>