<?php
/**
 * 配置模型
 * User: Intril.leng
 * Date: 2016/1/12
 * Time: 18:57
 */

Cool::auto_load('Model', 'model');
class ConfigModel extends Model {

    /**
     * 连接的DB
     * @var string
     */
    protected $db = 'config';

    /**
     * 查询项目数据
     * @param null $where
     * @param null $fields
     * @param      $page_num
     * @param      $page_size
     * @param bool $is_count
     * @return array
     * @throws CoolException
     */
    public function find_list ( $where = NULL, $page_num = null, $page_size = null, $is_count = false ) {
        if ( $page_size && $page_num ) {
            $this->limit ( $page_num, $page_size );
        }
        $return = $this->table ( 'project' )
            ->where ( $where )
            ->order_by(array('create_time' => 'DESC'))
            ->find_all();
        if ($is_count == TRUE) {
            $count = $this->table('project')->where($where)->count();
            return array ( 'total' => $count, 'datalist' => $return );
        }else{
            return $return;
        }
    }

    /**
     * 查询一条
     * @param $where
     */
    public function find_by_id($id){
        if (empty($id)) {
            return false;
        }
        $return = $this->table('project')
            ->where(array('id' => $id))
            ->find_one();
        return $return;
    }

    /**
     * 更新数据
     *
     * @param      $where
     * @param      $data
     * @param null $cache_name
     * @param null $rkey
     * @param int  $expire
     * @return bool
     * @throws CoolException
     */
    public function updated($data, $where) {
        if (empty($data) || empty($where) ) {
            return false;
        }
        $return = $this->table('project')
            ->data($data)
            ->where($where)
            ->update();
        return $return;
    }

    /**
     * 添加数据
     *
     * @param      $data
     * @param null $cache_name
     * @param null $rkey
     * @param int  $expire
     * @param null $cache_insert_id
     * @return bool
     */
    public function add($data) {
        if (empty($data)) {
            return false;
        }
        $insert_id = $this->table('project')
            ->data($data)
            ->insert();
        return $insert_id;
    }

    /**
     * 删除数据
     *
     * @param      $where
     * @param null $cache_name
     * @param null $rkey
     * @return bool
     * @throws CoolException
     */
    public function remove($where) {
        if (empty($where)) {
            return false;
        }
        $return = $this->table('project')
            ->where($where)
            ->delete();
        return $return;
    }

}