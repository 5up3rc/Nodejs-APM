<?php
/**
 * 登录控制器
 *
 * @author       Intril.Leng <jj.comeback@gmail.com>
 * @Date         2015年8月7日
 * @Time         下午3:18:48
 */
class LoginController extends Controller {

    /**
     * 登录页
     */
    public function index () {
        $err = fetch_val ( 'get.error', 0 );
        $error = array( 1 => '帐号密码错误,请重新登录', 2 => '帐号被封,请联系管理员', 0 => '' );
        $this->display ( 'login', array( 'err' => $error[$err] ) );
    }

    /**
     * 登出
     */
    public function logout () {
        Cool::session ()->destroy ();
        $this->redirect ( '/login/index', 302 );
    }

    /**
     * 检查登录
     */
    public function dologin () {
        $user_name = fetch_val ( 'post.user_name' );
        $password = fetch_val ( 'post.password' );
        $where = array( 'user_name' => $user_name, 'password' => $password );
        $user_info = Cool::model ( 'Admin' )->return_admin ( 'admin_user', $where );
        if ( empty( $user_info ) ) {
            $this->json ( 1, '帐号密码错误,请重试' );
        }
        $user_info = current ( $user_info );
        if ( $user_info['status'] != 1 ) {
            $this->json ( 1, '您登录的用户未激活,请联系管理员' );
        }
        // set to session
        $user_data = array(
            'user_id'    => $user_info['user_id'],
            'user_name'  => $user_info['user_name'],
            'real_name'  => $user_info['real_name'],
            'mobile'     => $user_info['mobile'],
            'email'      => $user_info['email'],
            'login_time' => $user_info['login_time'],
            'user_group' => $user_info['user_group'],
            'template'   => $user_info['template'],
        );
        $group_info = Cool::model ( 'Admin' )->return_admin ( 'admin_user_group', array( 'group_id' => $user_data['user_group'] ) );
        if ( !empty( $group_info ) ) {
            $group_info = current ( $group_info );
        }
        $user_data['group_role'] = empty( $group_info['group_role'] ) ? -1 : $group_info['group_role'];
        Cool::session ()->set_data ( 'user_data', $user_data );
        Cool::session ()->set_data ( 'user_id', $user_info['user_id'] );
        Cool::session ()->set_data ( 'user_name', $user_info['user_name'] );
        $this->json ( 0, array( 'index' => '/admin/index', 'msg' => '登录成功' ) );
    }
}