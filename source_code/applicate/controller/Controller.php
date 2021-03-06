<?php

/**
 * 共公控制器，业务中共公的方法
 *
 * @author       Intril.Leng <jj.comeback@gmail.com>
 * @Date         2015年8月7日
 * @Time         下午3:18:48
 */
class Controller extends CoolController {

    /**
     * 返回数据默认条数
     * @var number
     */
    protected $page_size;

    /**
     * 默认页码
     * @var number
     */
    protected $page_num;

    /**
     * 用户信息(session信息)
     *
     * @var null
     */
    public $user_data = null;

    /**
     * 当前登录用户id
     *
     * @var null
     */
    public $uid = null;


    /**
     * 页面默认数据
     * @var array
     */
    public $data = array(
        'title' => '后台管理系统',
    );

    /**
     * 自动加载的方法
     *
     * @see CoolController::before()
     */
    public function before($resource, $action) {
        header('Content-type:text/html;charset=utf-8');
        Cool::auto_load('function', 'helper');
        $this->page_size = fetch_val ( 'request.page_size', Cool::$GC ['page_size'] );
        $this->page_num = fetch_val ( 'request.page_num', 0 );
        if ($resource != 'LoginController' && $this->check_login() == false) {
            echo '没权限,谢谢';
            exit();
        }
        parent::before($resource, $action);
    }

    /**
     * 检查登录
     *
     * @return bool
     */
    public function check_login() {
        $this->user_data = Cool::session()->get_data('user_data');
        $this->uid = $this->user_data['user_id'];
        if ($this->uid == null || $this->user_data == null) {
            $this->redirect('/login/index');
        }
        $this->data['user'] = $this->user_data;
        return true;
    }

    /**
     * 返回JSON
     *
     * @param string $msg
     * @param number $code
     */
    public function json ( $code = 1, $msg = NULL ) {
        $data = array (
            'code' => $code,
            'timestamp' => time (),
            'msg' => $msg
        );
        $this->to_json ( $data );
    }

    /**
     * 分页
     * @param       $count
     * @param array $config
     * @return mixed
     */
    public function pager($count, $config = array()){
        $default = array (
            'current_page' => array (
                'source' => 'query_string',
                'key' => 'page'
            ),
            'total_items' => $count,
            'items_per_page' => $this->perpage,
            'view' => 'common/pagination',
        );
        if (!empty($config)){
            $default = array_merge($default, $config);
        }
        $pager = Pagination::factory ( $default );
        return $pager;
    }
}

