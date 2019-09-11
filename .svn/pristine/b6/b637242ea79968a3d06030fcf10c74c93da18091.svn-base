<?php
/**
 * User: 王模刚
 * Date: 2017/11/4
 * Time: 16:17
 */

namespace Report\Model;


use Think\Model;

class ReportOrderBoard2Model extends Model
{
    protected $tableName = 'report_order_board_2';

    public function getDataList(){
        $date = date('Y-m-d',strtotime('-1 month'));
        if($date < '2017-11-04'){
                $date = '2017-11-04';
        }
        $where['to_date'] = ['egt',$date];
        return $this->where($where)->order('to_date desc')->select();
    }

}