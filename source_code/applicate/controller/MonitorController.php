<?php
/**
 * 监控系统模块
 * User: Intril.Leng <jj.comeback@gmail.com>
 * Date: 2016/1/12
 * Time: 18:37
 */

class MonitorController extends Controller {
    /**
     * 项目管理
     * @return View
     */
    public function manager () {
        $params = fetch_val ( 'get.' );
        $project_name = fetch_val ( 'post.name', '' );
        if ( $project_name ) {
            $where = array( 'name' => array( "%" . $project_name . "%", 'LIKE' ) );
        } else {
            $where = array();
        }
        $return = Cool::model ( 'Config' )->find_list ( $where, $this->page_num, $this->page_size );
        $this->display ( 'monitor/manager', array( 'params' => $params, 'list' => $return ) );
    }

    /**
     * 编辑/修改
     * @throws CoolException
     */
    public function modify () {
        $id = fetch_val ( 'get.id', 0 );
        if ( $id ) {
            $data = Cool::model ( 'Config' )->find_by_id ( $id );
        } else {
            $data = array( 'id' => '', 'name' => '', 'dept' => '', 'leader' => '', 'tel' => '', 'desc' => '' );
        }
        $this->display ( 'monitor/project_modify', $data );
    }

    /**
     * 保存数据
     * @throws CoolException
     */
    public function save () {
        $data = fetch_val ( 'post.' );
        if ( isset( $data['id'] ) && $data['id'] ) { // update
            $update = array(
                'name'   => $data['name'], 'dept' => $data['dept'],
                'leader' => $data['leader'], 'tel' => $data['tel'], 'desc' => $data['desc']
            );
            $return = Cool::model ( 'Config' )->updated ( $update, array( 'id' => $data['id'] ) );
        } else {
            $insert = array(
                'name'   => $data['name'], 'dept' => $data['dept'],
                'leader' => $data['leader'], 'tel' => $data['tel'],
                'desc'   => $data['desc'], 'create_time' => time (),
            );
            $return = Cool::model ( 'Config' )->add ( $insert );
        }
        if ( $return ) {
            $this->json ( 1, array( 'act' => 'close', 'data' => '操作成功' ) );
        } else {
            $this->json ( 1, array( 'act' => 'error', 'data' => '操作失败' ) );
        }
    }

    /**
     * 删除
     * @return View
     */
    public function remove(){
        $id = fetch_val('get.id', 0);
        if ($id == 0) {
            $this->json ( 1, array( 'act' => 'alert', 'data' => '删除失败' ) );
        }
        if ( Cool::model ( 'Config' )->remove ( array( 'id' => $id ) ) ) {
            $this->json ( 1, array( 'act' => 'refresh', 'data' => '删除成功' ) );
        } else {
            $this->json ( 1, array( 'act' => 'alert', 'data' => '删除失败' ) );
        }

    }

    /**
     * API流水统计
     * @return View
     */
    public function access () {
        $search['cmd'] = fetch_val ( 'get.cmd', '' );
        $search['request_date'] = fetch_val ( 'get.request_date', date ( 'Y-m-d' ) );
        $search['project_id'] = fetch_val ( 'get.project_id', 1 );
        $where['request_date'] = $search['request_date'];
        $where['project_type'] = $search['project_id'];
        if ( $search['cmd'] != '' ) {
            $where['cmd'] = array( "%" . $search['cmd'] . "%", 'LIKE' );
        }
        $data = Cool::model ( 'Report' )->minute_access_stats ( $where );
        $project = Cool::model('Config')->find_list();
        $view = array( 'list' => $data, 'search' => $search, 'project' => array_change_key($project, 'id', 'name') );
        $this->display ( 'monitor/access', $view );
    }

    /**
     * API 10分钟总量走势
     * @return View
     */
    public function request_trend () {
        $search['goback'] = fetch_val('get.goback', 0);
        $search['cmd'] = fetch_val ( 'get.cmd', '' );
        $search['request_date'] = fetch_val ( 'get.request_date', date ( 'Y-m-d' ) );
        $search['project_id'] = fetch_val ( 'get.project_id', 1 );
        $where['request_date'] = $search['request_date'];
        $where['project_type'] = $search['project_id'];
        if (!empty($search['cmd'])) {
            $where['cmd'] = $search['cmd'];
        }
        $request_date = Cool::model ( 'Report' )->request_trend ( $where );
        $where['request_date'] = date ( 'Y-m-d', strtotime ( $search['request_date'] ) - 86400 );
        $l_request_date = Cool::model ( 'Report' )->request_trend ( $where );
        $where['request_date'] = date ( 'Y-m-d', strtotime ( $search['request_date'] ) - 86400 * 7 );
        $ls_request_date = Cool::model ( 'Report' )->request_trend ( $where );
        $project = Cool::model('Config')->find_list();
        $view = array(
            'day' => $request_date, 'l_day' => $l_request_date, 'ls_day' => $ls_request_date,
            'date' => $search['request_date'], 'l_date' => date ( 'Y-m-d', strtotime ( $search['request_date'] ) - 86400 ),
            'ls_date' => date ( 'Y-m-d', strtotime ( $search['request_date'] ) - 86400 * 7 ), 'search' => $search,
            'project' => array_change_key($project, 'id', 'name'),
        );
        $this->display ( 'monitor/request_trend', $view );
    }

    /**
     * 最大耗时详情
     * @return View
     */
    public function max_exectime_detail(){
        $search['goback'] = fetch_val('get.goback', 0);
        $search['cmd'] = fetch_val ( 'get.cmd' );
        $search['request_date'] = fetch_val ( 'get.request_date' );
        $search['project_id'] = fetch_val ( 'get.project_id' );
        $where = 'log_type = "Access" AND cmd = "'.$search['cmd'].'" AND project_type = "'.$search['project_id'].'" AND request_time >='.strtotime($search['request_date']).' AND request_time < '.strtotime($search['request_date']." 24:00:00");
        $return = Cool::model ( 'Report' )->max_exectime_detail ( $where );
        $view = array(
            'return' => $return, 'search' => $search
        );
        $this->display ( 'monitor/max_exectime_detail', $view );
    }

    /**
     * 主机请求分布(总量包抱错误请求)
     * @throws CoolException
     */
    public function host_access(){
        $search['cmd'] = fetch_val ( 'get.cmd', '' );
        $search['request_date'] = fetch_val ( 'get.request_date', date ( 'Y-m-d') );
        $search['project_id'] = fetch_val ( 'get.project_id', 1 );
        $request_time = strtotime($search['request_date']);
        $where = 'project_type = '.$search['project_id'].' and request_time >= '.$request_time.' and request_time < '.($request_time+86400);
        if ( $search['cmd'] != '' ) {
            $where .= ' and cmd like "%'.$search['cmd'].'%"';
        }
        $data = Cool::model ( 'Report' )->host_report ( $where );
        $project = Cool::model ( 'Config' )->find_list ();
        $view = array( 'list' => $data, 'search' => $search, 'project' => array_change_key ( $project, 'id', 'name' ) );
        $this->display ( 'monitor/host_access', $view );
    }

    /**
     * 平均每分钟请求耗时
     * @throws CoolException
     */
    public function minute_avg_time (){
        $search['cmd'] = fetch_val ( 'get.cmd' );
        $search['request_date'] = fetch_val ( 'get.request_date', date ( 'Y-m-d') );
        $search['project_id'] = fetch_val ( 'get.project_id', 1 );
        $his = date ( 'H:i:00', strtotime ( '-3 minute' ) );
        $start_time = strtotime ( $search['request_date'] . " " . $his );
        $ReportModel = Cool::model('Report');
        $where = array( 'project_type' => $search['project_id'], 'log_type' => 'Access' );
        if (!empty($search['cmd'])){
            $where['cmd'] = $search['cmd'];
        }
        $out_data = array();
        //查询各秒数据
        for ( $i = 0; $i < 60; $i++ ) {
            $r_time = $start_time + $i;
            $out_data[$i]['request_time'] = date("Y-m-d H:i:s", $r_time);
            $out_data[$i]['exectime'] = 0;
            $where['request_time'] = $r_time;
            $data = $ReportModel->find_list ( $where, 'exectime,request_time' ); // 数据并发时，产生多条记录
            //填充统计点数据
            $count = 0;
            foreach ( $data as $k => $row ) {
                $out_data[$i]['exectime'] += $row['exectime'];
                $count++;
            }
            if ( $out_data[$i]['exectime'] > 0 ) {
                $out_data[$i]['exectime'] = round($out_data[$i]['exectime'] / $count, 4);
            }
        }
        $project = Cool::model ( 'Config' )->find_list ();
        $view = array(
            'table_data' => $out_data, 'search' => $search, 'project' => array_change_key ( $project, 'id', 'name' )
        );
        $this->display('monitor/minute_avg_time', $view);
    }

    /**
     * API错误详情
     * @throws CoolException
     */
    public function error(){
        $search['cmd'] = fetch_val ( 'get.cmd', '' );
        $search['request_date'] = fetch_val ( 'get.request_date', date ( 'Y-m-d' ) );
        $search['project_id'] = fetch_val ( 'get.project_id', 1 );
        $request_time = strtotime($search['request_date']);
        $where = 'project_type = '.$search['project_id'].' and request_time >= '.$request_time.' and request_time < '.($request_time+86400).' and log_type = "Error"';
        if ( $search['cmd'] != '' ) {
            $where .= ' and cmd like "%'.$search['cmd'].'%"';
        }
        $data = Cool::model ( 'Report' )->error_report ( $where );
        $project = Cool::model('Config')->find_list();
        $view = array( 'list' => $data, 'search' => $search, 'project' => array_change_key($project, 'id', 'name') );
        $this->display('monitor/error', $view);
    }

    /**
     * 错误详细信息
     * @throws CoolException
     */
    public function error_detail(){
        $search['goback'] = fetch_val('get.goback', 0);
        $search['cmd'] = fetch_val ( 'get.cmd' );
        $search['request_date'] = fetch_val ( 'get.request_date' );
        $search['project_id'] = fetch_val ( 'get.project_id' );
        $request_time = strtotime($search['request_date']);
        $where = 'cmd = "'.$search['cmd'].'" and project_type = '.$search['project_id'].' and request_time >= '.$request_time.' and request_time < '.($request_time+86400).' and log_type = "Error"';
        $data = Cool::model ( 'Report' )->find_list ( $where );
        $view = array( 'list' => $data, 'search' => $search );
        $this->display('monitor/error_detail', $view);
    }

    /**
     * 主机错误分布
     * @throws CoolException
     */
    public function host_error(){
        $search['cmd'] = fetch_val ( 'get.cmd', '' );
        $search['request_date'] = fetch_val ( 'get.request_date', date ( 'Y-m-d') );
        $search['project_id'] = fetch_val ( 'get.project_id', 1 );
        $request_time = strtotime($search['request_date']);
        $where = 'project_type = '.$search['project_id'].' and request_time >= '.$request_time.' and request_time < '.($request_time+86400).' and log_type = "Error"';
        if ( $search['cmd'] != '' ) {
            $where .= ' and cmd like "%'.$search['cmd'].'%"';
        }
        $data = Cool::model ( 'Report' )->host_report ( $where );
        $project = Cool::model ( 'Config' )->find_list ();
        $view = array( 'list' => $data, 'search' => $search, 'project' => array_change_key ( $project, 'id', 'name' ) );
        $this->display('monitor/host_error', $view);
    }

}