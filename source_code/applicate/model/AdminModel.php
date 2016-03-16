<?php
/**
 * 后台管理功能登录
 * User: guosen
 * Date: 2016/1/8
 * Time: 16:17
 */
Cool::auto_load('Model', 'model');
class AdminModel extends Model {

    /**
     * 返回admin中的产品
     * @param string $where
     * @return Ambigous <multitype:, multitype:unknown NULL >
     */
    public function return_admin ( $table, $where = null ) {
        if ( !empty( $where ) ) {
            $this->where ( $where );
        }
        $this->table ( $table );
        $return = $this->find_all ();
        return $return;
    }

    public function count_admin($table, $where = NULL){
        $count = $this->table($table)
            ->where($where)
            ->count();
        return $count;
    }

    /**
     * 更新admin
     * @param unknown $data
     * @param string $where
     * @return Ambigous <unknown_type, object, mixed, number, Database_Result_Cached, multitype:>
     */
    public function update_admin ( $table, $data, $where = NULL ) {
        if ( $where == NULL || empty( $data ) || $table == null ) {
            return false;
        }
        // 写日志
        $logs_data = array(
            'action' => 'update',
            'result' => array( 'table' => $table, 'data' => $data, 'where' => $where ),
        );
        $this->insert_logs ( $logs_data );
        return $this->table ( $table )->where ( $where )
            ->data ( $data )
            ->update ();
    }

    /**
     * 写日志方法
     * @param unknown $inser_data
     * @return Ambigous <unknown_type, object, mixed, number, Database_Result_Cached, multitype:>
     */
    public function insert_logs ( $data ) {
        $user_data = Cool::session ()->get_data ( 'user_data' );
        $data['user_name'] = $user_data['user_name'];
        $data['class_name'] = Router::current_uri ();
        $module_info = $this->return_admin ( 'admin_menu_url', 'menu_url = "/' . $data['class_name'] . '"' );
        if ( !empty( $module_info ) ) {
            $data['class_obj'] = $module_info[0]['menu_name'];
        }
        $data['op_time'] = time ();
        $data['result'] = urlencode ( json_encode ( $data['result'] ) );
        return $this->table ( 'admin_sys_log' )
            ->data ( $data )
            ->insert ();
    }

    /**
     * 插入admin
     * @param unknown $data
     */
    public function insert_admin ( $table, $data ) {
        if ( empty( $data ) || $table == null ) {
            return false;
        }
        // 写日志
        $logs_data = array(
            'action' => 'insert',
            'result' => array( 'table' => $table, 'data' => $data ),
        );
        $this->insert_logs ( $logs_data );
        return $this->table ( $table )->data ( $data )
            ->insert ();
    }

    /**
     * 删除数据
     * @param unknown $table
     * @param unknown $where
     * @return boolean|Ambigous <unknown_type, object, mixed, number, Database_Result_Cached, multitype:>
     */
    public function delete_admin ( $table, $where ) {
        if ( empty( $table ) || empty( $where ) ) {
            return false;
        }
        // 写日志
        $logs_data = array(
            'action' => 'delete', 'result' => array( 'table' => $table, 'data' => $where ),
        );
        $this->insert_logs ( $logs_data );
        return $this->table ( $table )
            ->where ( $where )
            ->delete ();
    }
}