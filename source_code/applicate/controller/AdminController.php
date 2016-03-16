<?php

/**
 * 后台管理系统模块
 * User: Intril.Leng <jj.comeback@gmail.com>
 * Date: 2016/1/8
 * Time: 16:09
 */
class AdminController extends Controller {

    /**
     * 登录后台默认调用位置(导航数据初始化)
     *
     * @param       $view
     * @param array $data
     * @throws CoolException
     */
    public function index () {
        // 处理导行的问题
        $navigate_id = fetch_val('get.navigate_id');
        // sidebar 部分数据
        $this->data['navigate'] = $this->get_navigate ();
        if ( $navigate_id && isset( $this->data['navigate'][$navigate_id] ) ) {
            $this->data['sidebar'] = $this->data['navigate'][$navigate_id];
        } else {
            $this->data['sidebar'] = current ( $this->data['navigate'] );
        }
        Cool::session()->set_data('navigate_id', $this->data['sidebar']['navigate_id']);
        $this->data['sidebar'] = $this->data['sidebar']['module'];
        $this->data['footer'] = array(
            'version' => 'v.0.1', 'author' => 'Seven.Leng', 'email' => 'Seven.Leng@top25.cn'
        );
        $this->data['sess'] = Cool::session()->get_data('user_data');
        $this->data['navigate_id'] = Cool::session()->get_data( 'navigate_id' );
        $this->display('index', $this->data);
    }

    /**
     * 获取导行条
     */
    private function get_navigate () {
        $user_data = Cool::session()->get_data('user_data');
        $table = "admin_menu_url, admin_module";
        if ( $user_data['user_group'] == 1 ) { // admin 帐号所有权限
            $where = "admin_menu_url.module_id = admin_module.module_id and admin_module.module_online = 1 and admin_menu_url.menu_online = 1 and admin_menu_url.is_show = 1";
        } else {
            $where = "admin_module.module_online = 1 and admin_menu_url.menu_online = 1 and admin_menu_url.module_id = admin_module.module_id and admin_menu_url.is_show = 1 and admin_menu_url.menu_id in (" . $user_data['group_role'] . ")";
        }
        $where .= " ORDER BY admin_module.module_sort asc, admin_menu_url.menu_id asc";
        $menu = Cool::model('Admin')->return_admin ( $table, $where );
        if ( empty( $menu ) ) {
            return array();
        }
        if ( $user_data['user_group'] == 1 ) {
            $nav_where = '1 = 1';
        } else {
            $nav_where = 'navigate_id in (' . trim ( $user_data['nav_id'], ',' ) . ')';
        }
        $navigate = Cool::model('Admin')->return_admin ( 'admin_navigate', 'navigate_online = 1 and ' . $nav_where . ' order by navigate_sort asc' );
        if ( empty( $navigate ) ) {
            return array();
        }
        $list_nav = array();
        foreach ( $navigate as $key => $value ) {
            $module_id_array = explode ( ',', $value['module_id'] );
            foreach ( $menu as $k => $v ) {
                if ( in_array ( $v['module_id'], $module_id_array ) ) {
                    $list_nav[$value['navigate_id']]['navigate_id'] = $value['navigate_id'];
                    $list_nav[$value['navigate_id']]['navigate_name'] = $value['navigate_name'];
                    $list_nav[$value['navigate_id']]['navigate_url'] = $value['navigate_url'] . '?navigate_id=' . $value['navigate_id'];
                    $list_nav[$value['navigate_id']]['navigate_icon'] = $value['navigate_icon'];
                    $list_nav[$value['navigate_id']]['module'][$v['module_id']]['module_id'] = $v['module_id'];
                    $list_nav[$value['navigate_id']]['module'][$v['module_id']]['module_name'] = $v['module_name'];
                    $list_nav[$value['navigate_id']]['module'][$v['module_id']]['module_icon'] = $v['module_icon'];
                    $list_nav[$value['navigate_id']]['module'][$v['module_id']]['menu'][$v['menu_id']] = $v;
                }
            }
        }
        return $list_nav;
    }


    /**
     * 用户管理
     */
    public function users () {
        $params = fetch_val ( 'get.' );
        $where = '1 = 1';
        if ( !empty( $params['user_name'] ) ) {
            $where .= ' AND user_name LIKE "%' . $params['user_name'] . '%"';
        } else {
            $params['user_name'] = '';
        }
        if ( !empty( $params['user_group'] ) ) {
            $where .= ' AND user_group = ' . $params['user_group'];
        } else {
            $params['user_group'] = 0;
        }
        $user_list = Cool::model ( 'Admin' )->return_admin ( 'admin_user', $where );
        $group_list = Cool::model ( 'Admin' )->return_admin ( 'admin_user_group' );
        $view = array(
            'params'      => $params, 'user_list' => $user_list,
            'user_status' => array( 1 => '启用', 0 => '禁用' ),
            'group_list'  => array_change_key ( $group_list, 'group_id', 'group_name' ),
        );
        $this->display ( 'admin/users_list', $view );
    }

    /**
     * 用户修改
     */
    public function modify () {
        $user_id = fetch_val ( 'get.user_id' );
        if ( $user_id ) {
            $admin_data = Cool::model ( 'Admin' )->return_admin ( 'admin_user', array( 'user_id' => $user_id ) );
            $admin_data = current ( $admin_data );
        } else {
            $admin_data = array(
                'user_id'   => '','user_name' => '','real_name' => '','mobile'    => '',
                'email'     => '','user_desc' => '','user_group' => '',
            );
        }
        $group_list = Cool::model ( 'Admin' )->return_admin ( 'admin_user_group' );
        $data = array(
            'sess'       => Cool::session ()->get_data ( 'user_data' ), 'input' => $admin_data,
            'params'     => fetch_val ( 'get.' ),
            'group_list' => array_change_key ( $group_list, 'group_id', 'group_name' ),
        );
        $this->display ( 'admin/user_edit', $data );
    }

    /**
     * 用户组
     */
    public function groups () {
        $params = fetch_val ( 'get.' );
        $group_list = Cool::model ( 'Admin' )->return_admin ( 'admin_user_group' );
        $user_list = Cool::model ( 'Admin' )->return_admin ( 'admin_user' );
        $user_list = array_change_key ( $user_list, 'user_id', 'user_name' );
        $view = array(
            'group_list' => $group_list,
            'user_list'  => $user_list,
            'params'     => $params,
        );
        $this->display ( 'admin/group_list', $view );
    }

    /**
     * 用户组修改，添加
     */
    public function group_modify () {
        $group_id = fetch_val ( 'get.group_id', 0 );
        if ( $group_id ) {
            $admin_data = Cool::model ( 'Admin' )->return_admin ( 'admin_user_group', array( 'group_id' => $group_id ) );
            if ( empty ( $admin_data ) ) {
                $this->__redirect ( 'admin/groups', '用户组参数错误', true );
            }
            $admin_data = current ( $admin_data );
        }else{
            $admin_data = array('group_id' => '', 'group_name' => '', 'group_desc' => '');
        }
        $data = array(
            'input' => $admin_data, 'params' => fetch_val ( 'get.' ),
        );
        $this->display ( 'admin/group_edit', $data );
    }

    public function group_role () {
        $group_id = fetch_val ( 'get.group_id', 0 );
        $group_role = Cool::model ( 'Admin' )->return_admin ( 'admin_user_group', array( 'group_id' => $group_id ) );
        if ( empty( $group_role ) ) {
            $this->__redirect ( '/admin/groups', '权限设置参数有错', true );
        }
        $group_role = current ( $group_role );
        $has_group = explode ( ',', $group_role['group_role'] );
        $table = 'admin_menu_url, admin_module';
        $where = 'admin_menu_url.module_id = admin_module.module_id';
        $menu_info = Cool::model ( 'Admin' )->return_admin ( $table, $where );
        $menu_list = $menu_id = array();
        foreach ( $menu_info as $key => $value ) {
            if ( in_array ( $value['menu_id'], $has_group ) ) {
                $menu_id[$value['module_name']][] = $value['menu_id'];
            }
            $menu_list[$value['module_name']][$value['menu_id']] = $value['menu_name'];
        }
        $view = array(
            'group_role' => $group_role, 'value' => $menu_id,
            'menu_list'  => $menu_list, 'params' => fetch_val ( 'get.' ),

        );
        $this->display ( 'admin/group_role', $view );
    }

    /**
     * 功能模块
     */
    public function modules () {
        $params = fetch_val ( 'get.' );
        $group_list = Cool::model ( 'Admin' )->return_admin ( 'admin_module' );
        $view = array(
            'group_list' => $group_list,
            'params'     => $params,
            'status'     => array( 1 => '在线', 0 => '下线' )
        );
        $this->display ( 'admin/modules_list', $view );
    }

    /**
     * 模块修改添加
     */
    public function module_modify () {
        $module_id = fetch_val ( 'get.module_id' );
        if ( $module_id ) {
            $admin_data = Cool::model ( 'Admin' )->return_admin ( 'admin_module', array( 'module_id' => $module_id ) );
            if ( empty ( $admin_data ) ) {
                $this->__redirect ( 'admin/moduels', '模块参数错误', true );
            }
            $admin_data = current ( $admin_data );
        } else {
            $admin_data = array(
                'module_id' => '', 'module_name' => '', 'module_sort' => '', 'module_online' => '', 'module_desc' => ''
            );
        }
        $data = array(
            'input' => $admin_data, 'params' => fetch_val ( 'get.' ),
        );
        $this->display ( 'admin/modules_edit', $data );
    }

    /**
     * 功能列表
     */
    public function menus () {
        $params = fetch_val ('get.');
        $table = 'admin_menu_url, admin_module';
        $where = 'admin_menu_url.module_id = admin_module.module_id';
        if ( !empty( $params['menu_name'] ) ) {
            $where .= ' AND menu_name LIKE "%' . $params['menu_name'] . '%"';
        } else {
            $params['menu_name'] = '';
        }
        if ( !empty( $params['module_id'] ) ) {
            $where .= ' AND admin_menu_url.module_id = ' . $params['module_id'];
        } else {
            $params['module_id'] = 0;
        }
        $menu_list = Cool::model ( 'Admin' )->return_admin ( $table, $where );
        $module = Cool::model ( 'Admin' )->return_admin ( 'admin_module', 'module_online = 1' );
        $view = array(
            'params'      => $params,
            'module_list' => array( 0 => '所有模块' ) + array_change_key ( $module, 'module_id', 'module_name' ),
            'menu_list'   => $menu_list,
            'status'     => array(0 => '否', 1 => '是')
        );
        $this->display ( 'admin/menu_list', $view );
    }

    /**
     * 功能修败
     */
    public function menu_modify () {
        $menu_id = fetch_val( 'get.menu_id' );
        if ( $menu_id ) {
            $admin_data = Cool::model ( 'Admin' )->return_admin ( 'admin_menu_url', array('menu_id' => $menu_id) );
            if ( empty ( $admin_data ) ) {
                $this->__redirect ( 'admin/menus', '功能列表参数错误', true );
            }
            $admin_data = current ( $admin_data );
        } else {
            $admin_data = array(
                'menu_id' => '', 'menu_name' => '', 'menu_url' => '', 'module_id' => '',
                'menu_desc' => '', 'is_show' => '', 'menu_online' => 1);
        }
        $module_list = Cool::model ( 'Admin' )->return_admin ( 'admin_module' );
        $this->display ( 'admin/menu_edit', array(
            'input'       => $admin_data, 'params' => fetch_val ('get.'),
            'module_list' => array_change_key ( $module_list, 'module_id', 'module_name' ),
        ) );
    }

    /**
     * 导行管理
     */
    public function navigate () {
        $params = fetch_val ( 'get.' );
        $where = '';
        if ( !empty( $params['navigate_name'] ) ) {
            $where['navigate_name'] = array( "%" . $params['navigate_name'] . "%", 'LIKE' );
        } else {
            $params['navigate_name'] = '';
        }
        if ( !empty( $params['module_id'] ) ) {
            $where['module_id'] = array( $params['module_id'], "FIND_IN_SET" );
        } else {
            $params['module_id'] = 0;
        }
        $navigate_list = Cool::model ( 'Admin' )->return_admin ( 'admin_navigate', $where );
        $module = Cool::model ( 'Admin' )->return_admin ( 'admin_module', 'module_online = 1' );
        $view = array(
            'params'      => $params,
            'module_list' => array( 0 => '所有模块' ) + array_change_key ( $module, 'module_id', 'module_name' ),
            'list'        => $navigate_list,
            'status'      => array( 0 => '否', 1 => '是' )
        );
        $this->display ( 'admin/navigate_list', $view );
    }

    /**
     * 导行编辑
     */
    public function navigate_modify () {
        $navigate_id = fetch_val ( 'get.navigate_id' );
        if ( $navigate_id ) {
            $admin_data = Cool::model ( 'Admin' )->return_admin ( 'admin_navigate', array('navigate_id' => $navigate_id) );
            if ( empty ( $admin_data ) ) {
                $this->__redirect ( 'admin/navigate', '导行列表参数错误', true );
            }
            $admin_data = current ( $admin_data );
        } else {
            $admin_data = array(
                'navigate_name' => '', 'navigate_url' => '', 'navigate_icon' => '', 'navigate_sort' => '',
                'module_id' => '', 'navigate_online' => '', 'navigate_desc' => '', 'navigate_id' => ''
            );
        }
        $module_list = Cool::model ( 'Admin' )->return_admin ( 'admin_module' );
        $this->display ( 'admin/navigate_edit', array(
            'input'       => $admin_data, 'params' => fetch_val ('get.'),
            'module_list' => array_change_key ( $module_list, 'module_id', 'module_name' ),
        ) );
    }

    /**
     * 操作日志
     */
    public function syslog () {
        $where = " 1 = 1 order by op_time desc ";
        $syslog_list = Cool::model ( 'Admin' )->return_admin ( 'admin_sys_log', $where );
        $view = array(
            'syslog_list' => $syslog_list, 'params' => array()
        );
        $this->display ( 'admin/syslog', $view );
    }

    public function icon () {
        $this->display ( 'admin/icon' );
    }

    /**
     * 保存用户
     */
    public function save () {
        $params = fetch_val ( 'post.' );
        $type = $params ['type'];
        $id = $params ['id'];
        switch ( $params ['type'] ) {
            case 'user' :
                if ( !empty ( $params ['password'] ) ) {
                    $params ['password'] = md5 ( $params ['password'] );
                } else {
                    unset ( $params ['password'] );
                }
                $where = 'user_id = ' . $params ['id'];
                break;
            case 'user_group' :
                $where = 'group_id =' . $params ['id'];
                if ( !empty( $params['group_role'] ) ) {
                    foreach ( $params['group_role'] as $key => $val ) {
                        if ( empty( $params['group_role'][$key] ) ) {
                            unset( $params['group_role'][$key] );
                        }
                    }
                    $params['group_role'] = implode ( ',', $params['group_role'] );
                }
                break;
            case 'menu_url' :
                $where = 'menu_id =' . $params ['id'];
                break;
            case 'module' :
                $where = 'module_id =' . $params ['id'];
                break;
            case 'navigate' :
                $where = 'navigate_id = ' . $params['id'];
        }
        unset ( $params ['id'], $params ['type'] );
        if ( empty ( $id ) ) { // add
            $return = Cool::model ( 'Admin' )->insert_admin ( 'admin_' . $type, $params );
        } else { // modify
            $return = Cool::model ( 'Admin' )->update_admin ( 'admin_' . $type, $params, $where );
        }
        if ( $return ) {
            $this->json ( 1, array( 'act' => 'close', 'data' => '操作成功' ) );
        } else {
            $this->json ( 1, array( 'act' => 'error', 'data' => '操作失败' ) );
        }
    }

    /**
     * 删除用户
     */
    public function del () {
        $params = fetch_val('get.');
        $id = $params ['id'];
        if ( empty ( $id ) ) {
            $this->json ( 1, array( 'act' => 'alert', 'data' => '删除数据参数错误' ) );
        }
        if ( $params ['type'] == 'user' ) {
            $where = 'user_id = ' . $id;
        }
        if ( $params ['type'] == 'user_group' ) {
            $where = 'group_id = ' . $id;
        }
        if ( $params['type'] == 'module' ) {
            $where = 'module_id = ' . $id;
        }
        if ( $params['type'] == 'menu_url' ) {
            $where = 'menu_id = ' . $id;
        }
        if ( $params['type'] == 'navigate' ) {
            $where = 'navigate_id = ' . $id;
        }
        if ( Cool::model ( 'Admin' )->delete_admin ( 'admin_' . $params ['type'], $where ) ) {
            $this->json ( 1, array( 'act' => 'refresh', 'data' => '删除成功' ) );
        } else {
            $this->json ( 1, array( 'act' => 'alert', 'data' => '删除失败' ) );
        }
    }

    /**
     * 用户封停解封
     */
    public function stop () {
        $params = fetch_val('get.');
        Cool::model ( 'Admin' )->update_admin ( 'admin_user', array(
            'status' => $params ['status']
        ), 'user_id = ' . $params ['user_id'] );
        $this->json ( 1, array( 'act' => 'refresh', 'data' => '操作成功' ) );
    }

}