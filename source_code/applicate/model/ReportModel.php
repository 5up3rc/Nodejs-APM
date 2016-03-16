<?php
/**
 * API请求流水统计数据
 * User: guosen
 * Date: 2016/1/27
 * Time: 14:30
 */

Cool::auto_load('Model', 'model');
class ReportModel extends Model{

    /**
     * 连接的DB
     * @var string
     */
    protected $db = 'infobright';

    /**
     * 获取流水日志统计
     * SELECT sum(`request_count`)*10 as counts, `cmd`, sum(exectime)/sum(`request_count`) as avg,
     * max(`request_max_time`) as max, min(`request_min_time`) as min
     * FROM `api_minute_stats`
     * WHERE `request_date` = '2016-01-27' AND `project_type` = 2
     * GROUP BY cmd
     * ORDER BY `counts` desc
     */
    public function minute_access_stats($where){
        $fields = array(
            'sum(`request_count`) as counts', '`cmd`', 'sum(exectime)/sum(`request_count`) as avg',
            'max(`request_max_time`) as max', 'min(`request_min_time`) as min', '`project_type`', '`request_date`'
        );
        $return = $this->table('api_minute_stats')
            ->fields(implode(',', $fields))
            ->where($where)
            ->group_by(array('cmd' => 'DESC'))
            ->order_by(array('counts' => 'DESC'))
            ->find_all();
        return $return;
    }

    /**
     * 获取每10分钟走势
     * SELECT `request_minute`, `cmd`, `request_count`
     * FROM `api_minute_stats`
     * WHERE `cmd` = 'weibo/check' AND `request_date` = '2016-01-28' AND `project_type` = 1
     * ORDER BY `request_minute` asc
     */
    public function request_trend ( $where ) {
        $fields = array( '`request_minute`', '`cmd`', '`request_count`' );
        $return = $this->table ( 'api_minute_stats' )
            ->fields ( implode ( ',', $fields ) )
            ->where ( $where )
            ->order_by ( array( 'request_minute' => 'ASC' ) )
            ->find_all ();
        return $return;
    }

    /**
     * 返回最大耗时详情 50条记录
     * SELECT `cmd`, `exectime`, `hostip`, `playerid`, `request_time`, `msg`
     * FROM `BH_t_linelog`
     * WHERE `cmd` = 'weibo/check' AND `request_time` >= 1453935600 AND `request_time` < 1454022000 AND `project_type` = 1
     * ORDER BY `exectime` desc LIMIT 50
     */
    public function max_exectime_detail($where){
        $fields = array( '`cmd`', '`exectime`', '`hostip`', '`playerid`', '`request_time`', '`msg`' );
        $return = $this->table('BH_t_linelog')
            ->fields(implode(',', $fields))
            ->where($where)
            ->order_by(array('exectime' => 'DESC'))
            ->limit(0, 50)
            ->find_all();
        return $return;
    }

    /**
     * 返回服务器IP的请求分布/流水/错误
     * SELECT count(`cmd`) as counts, `hostip`
     * FROM `BH_t_linelog`
     * WHERE `request_time` >= 1453935600 AND `request_time` < 1454022000 AND `project_type` = 2
     * GROUP BY hostip
     */
    public function host_report ( $where ) {
        $fields = array( 'count(`cmd`) as counts', '`hostip`' );
        $return = $this->table ( 'BH_t_linelog' )
            ->fields ( implode ( ',', $fields ) )
            ->where ( $where )
            ->group_by ( array( 'hostip' => '' ) )
            ->find_all ();
        return $return;
    }

    /**
     * 获取日志流水详细表的数据
     *
     * @param $where
     * @param $fields
     */
    public function find_list ( $where, $fields ) {
        $return = $this->table ( 'BH_t_linelog' )
            ->fields ( $fields )
            ->where ( $where )
            ->find_all ();
        return $return;
    }

    /**
     * API请求错误统计
     * @params $where
     * SELECT count(`cmd`) as counts, `cmd`, `errcode`, avg(`exectime`) as avg, max(`exectime`) as max, min(`exectime`) as min
     * FROM `BH_t_linelog`
     * WHERE `errcode` != 400 AND `request_time` >= 1457564400 AND `request_time` < 1457650800 AND `project_type` = 2 AND `log_type` = 'ERROR'
     * GROUP BY cmd, errcode ORDER BY `counts` desc
     */
    public function error_report ( $where ) {
        $fields = array( 'count(`cmd`) as counts', '`cmd`', '`errcode`');
        $return = $this->table('BH_t_linelog')
            ->fields(implode(',', $fields))
            ->where($where)
            ->group_by('cmd, errcode')
            ->order_by(array('counts' => 'DESC'))
            ->find_all();
        return $return;
    }

    public function host_access(){

    }
}