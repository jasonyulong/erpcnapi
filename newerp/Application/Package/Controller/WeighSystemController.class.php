<?php
/**
 * 称重规则设置
 * Created by PhpStorm.
 * User: xiao
 * Date: 2018/4/11
 * Time: 14:15
 */

namespace Package\Controller;

use Common\Controller\CommonController;
use Common\Model\ConfigsModel;
use Package\Model\WeighRuleModel;
use Package\Model\WeightingKeywordModel;
use Think\Log;
class WeighSystemController extends CommonController
{
    //日志文件名
    private $logName = '';

    /**
     * 当前控制器的初始化操作
     */
    public function _initialize() {
        parent::_initialize();
        $this->logName = C('LOG_PATH').'weigh_'.date('y_m_d').'.log';
    }

    /**
     * 称重规则设置首页
     * @author xiao
     * @date 2018-04-12
     */
    public function ruleIndex(){
        $fields = 'id,weight_begin,weight_end,allow_dif';
        $data = (new WeighRuleModel())->getAll($fields,'weight_begin asc');
        //获取配置是否开启限重
        $configModel = new ConfigsModel();
        $config = $configModel->field("id,limit_weight_status")->find();
        $this->assign('data',$data);
        $this->assign('config',$config);
        $this->display();
    }

    /**
     * 设置称重重量范围
     * @param data 主键id，没有为0
     * @param min 最小范围
     * @param max 最大范围
     * @param dif 允许误差
     * @return json
     * @author xiao
     * @date 2018-04-12
     */
    public function ruleSet(){
        $id = (int)$_POST['data'];
        $min = sprintf('%.1f', (float)$_POST['min']);
        $max = sprintf('%.1f', (float)$_POST['max']);
        $dif = sprintf('%.1f', (float)$_POST['dif']);
        if($dif == 0) $this->ajaxReturn(['status'=>0,'msg'=>'请填写允许差异']);
        //判断有没有修改
        if(($min == 0) && ($max == 0) && ($dif == 0)) $this->ajaxReturn(['status'=>0,'msg'=>'请确认有进行修改']);
        //判断修改值是否合理
        if(($min < 0) || ($max < 0) && ($dif< 0)) $this->ajaxReturn(['status'=>0,'msg'=>'请合理设置，不能存在负数']);
        //最大范围不能小于最小范围
        if($min >= $max) $this->ajaxReturn(['status'=>0,'msg'=>'请合理设置范围区间']);
        //误差不能大于范围
        if($dif > $max) $this->ajaxReturn(['status'=>0,'msg'=>'允许误差不能大于设置范围']);
        $WeighRuleModel = new WeighRuleModel();
        //验证是否重复设置
//        if($id > 0){
//            $this->ajaxReturn(['status'=>0,'msg'=>'暂不支持修改']);
//        }
        if(!$this->checkWeight($min,$max,$id)) $this->ajaxReturn(['status'=>0,'msg'=>'请勿设置重复的重量范围']);
        //拼装数据
        $data['weight_begin'] = $min;
        $data['weight_end'] = $max;
        $data['allow_dif'] = $dif;
        //日志记录
        $log = "[begin]操作人：".$_SESSION['truename']."\n";
        $log.='请求数据：'.'data=>'.$_POST['data'].',min=>'.$_POST['min'].',max=>'.$_POST['max'].',dif=>'.$_POST['dif']."\n";
        $log.='保存数据：'.'id=>'.$id.',weight_begin=>'.$min.",weight_end=>".$max.",allow_dif=>".$dif."\n";
        //判断是否有主键id，如果有就进行保存，否则插入
        if($id > 0){
            $map = "id = $id";
            $result = $WeighRuleModel->saveData($map,$data);
            //写日志，操作人是谁，什么时间操作，操作数据
            if($result){
                $log.='更新成功[end]'."\n";
                Log::write($log,Log::INFO,'',$this->logName);
                $data['id'] = $id;
                $this->ajaxReturn(['status'=>1,'msg'=>'更新成功','data'=>$data]);
            }else{
                $log.='更新失败：'.$WeighRuleModel->_sql().'[end]'."\n";
                Log::write($log,Log::INFO,'',$this->logName);
                $this->ajaxReturn(['status'=>0,'msg'=>'更新失败']);
            }
        }else{
            $resultId = $WeighRuleModel->insertData($data);
            if($resultId > 0){
                $log.='添加成功[end]'."\n";
                Log::write($log,Log::INFO,'',$this->logName);
                $data['id'] = $resultId;
                $this->ajaxReturn(['status'=>1,'msg'=>'添加成功','data'=>$data]);
            }else{
                $log.='添加失败'.$WeighRuleModel->_sql().'[end]'."\n";
                Log::write($log,Log::INFO,'',$this->logName);
                $this->ajaxReturn(['status'=>0,'msg'=>'添加失败']);
            }
        }
    }

    /**
     * 删除规则
     * @param data 主键id
     * @return json
     * @author xiao
     * @date 2018-04-12
     */
    public function ruleDelete(){
        //获取参数判断
        $id = (int)$_POST['data'];
        if($id <= 0) $this->ajaxReturn(['status'=>0,'msg'=>'参数错误']);
        //日志记录
        $log = "[begin]操作人：".$_SESSION['truename']."\n";
        $log.='请求数据：'.'data=>'.$_POST['data']."\n";
        $log.='保存数据：'.'id=>'.$id."\n";
        //开始删除
        $WeighRuleModel = new WeighRuleModel();
        $result = $WeighRuleModel->delete($id);
        if($result){
            $log.='删除成功[end]'."\n";
            Log::write($log,Log::INFO,'',$this->logName);
            $this->ajaxReturn(['status'=>1,'msg'=>'删除成功']);
        }else{
            $log.='删除失败'.$WeighRuleModel->_sql().'[end]'."\n";
            Log::write($log,Log::INFO,'',$this->logName);
            $this->ajaxReturn(['status'=>0,'msg'=>'删除失败']);
        }
    }

    /**
     * 判断设置区间是否重复
     * @param $min 设置开始值
     * @param $max 设置结束值
     * @return bool
     * @author xiao
     * @date 2018-04-13
     */
    private function checkWeight($min,$max,$id){
        $where = null;
        if($id > 0){
           $where['id'] = ['neq',$id];
        }
        $data = (new WeighRuleModel())->getAll('weight_begin,weight_end','',$where);
        for ($i=0;$i<count($data);$i++){
            //设置的不能包含
            if($data[$i]['weight_begin'] <= $min && $min < $data[$i]['weight_end']){
                return false;
            }
            if($data[$i]['weight_begin'] < $max && $max <= $data[$i]['weight_end']){
                return false;
            }
            //不能在已存在区间
            if(($data[$i]['weight_begin'] <= $min) && ($max <= $data[$i]['weight_end']) ){
                return false;
            }
            if(($data[$i]['weight_begin'] >= $min) && ($max >= $data[$i]['weight_end']) ){
                return false;
            }

        }
        return true;
    }

    /**
     * 开启、关闭限重功能
     * @author Shawn
     * @date 2018/12/24
     */
    public function changeLimitWeightStatus(){
        if(!isset($_POST['status']) || !isset($_POST['id'])){
            $this->ajaxReturn(['status'=>0,'msg'=>'参数错误']);
        }else{
            $status = (int)$_POST['status'];
            $id = (int)$_POST['id'];
            $statusName = $status == 1 ? '开启' : '关闭';
            $configModel = new ConfigsModel();
            $result = $configModel->where("id={$id}")->save(['limit_weight_status'=>$status]);
            if($result !== false){
                $file = dirname(dirname(THINK_PATH)).'/log/checkweight/'.date('YmdH').'.txt';;
                $log = '['.date('Y-m-d H:i:s').']操作人：'.$_SESSION['truename'].PHP_EOL;
                $log .= $statusName."称重限制重量功能".PHP_EOL;
                writeFile($file,$log);
                $this->ajaxReturn(['status'=>1,'msg'=>'设置成功']);
            }else{
                $this->ajaxReturn(['status'=>0,'msg'=>'设置失败']);
            }
        }
    }

    /**
     * 称重规则加权列表
     * @author Shawn
     * @date 2018/12/25
     */
    public function weightingIndex(){
        $weightingModel = new WeightingKeywordModel();
        $data = $weightingModel->order("sort asc,id asc")->select();
        $this->assign('data',$data);
        $this->display();
    }


    /**
     * 显示添加/修改页面
     * @author Shawn
     * @date 2018/12/25
     */
    public function addWeightingPage(){
        $id = (int)I("id");
        $data['id'] = $id;
        $data['keyword'] = '';
        $data['weighting'] = '';
        if($id > 0){
            $weightingModel = new WeightingKeywordModel();
            $map['id'] = $id;
            $weightData = $weightingModel->where($map)->find();
            if(!empty($weightData)){
                $data = $weightData;
            }
        }
        $this->assign("data",$data);
        $this->display();
    }

    /**
     * 保存新增/修改数据
     * @author Shawn
     * @date 2018/12/25
     */
    public function saveWeighting(){
        $id = (int)I('id');
        $keyword = trim(I('keyword'));
        $weight = (int)I('weight');
        if(empty($keyword)){
            $this->ajaxReturn(['status'=>'0','msg'=>'关键字不能为空']);
        }
        if($weight == 0){
            $this->ajaxReturn(['status'=>'0','msg'=>'重量不能为0']);
        }
        $weightingModel = new WeightingKeywordModel();
        $data['keyword'] = $keyword;
        $data['weighting'] = $weight;
        $log = '操作人：'.session("truename").PHP_EOL;
        if($id > 0){
            $map['id'] = $id;
            $oldData = $weightingModel->where($map)->find();
            if(empty($oldData)){
                $this->ajaxReturn(['status'=>'0','msg'=>'请检查数据是否已被删除']);
            }
            if($oldData['keyword'] != $data['keyword']){
                $log .= '修改关键字：'.$data['keyword']."修改之前为：".$oldData['keyword'].PHP_EOL;
            }
            if($oldData['weighting'] != $data['weighting']){
                $log .= '修改重量：'.$data['weighting']."修改之前为：".$oldData['weighting'].PHP_EOL;
            }
            $result = $weightingModel->where($map)->save($data);
        }else{
            $log .= '添加加权，加权关键字：'.$data['keyword']."，加权重量：".$data['weighting'].PHP_EOL;
            $data['sort'] = time();
            $result = $weightingModel->add($data);
        }
        if($result !== false){
            Log::write($log,Log::INFO,'',$this->logName);
            $this->ajaxReturn(['status'=>'1','msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>'0','msg'=>'操作失败']);
        }

    }

    /**
     * 删除设置
     * @author Shawn
     * @date 2018/12/25
     */
    public function delWeighting(){
        $id = (int)I('id');
        $weightingModel = new WeightingKeywordModel();
        $map['id'] = $id;
        $oldData = $weightingModel->where($map)->find();
        if(empty($oldData)){
            $this->ajaxReturn(['status'=>'0','msg'=>'请检查数据是否已被删除']);
        }
        $log = '操作人：'.session("truename").PHP_EOL;
        $log .= '删除加权数据,加权关键字：'.$oldData['keyword']."，加权重量：".$oldData['weighting'].PHP_EOL;
        $result = $weightingModel->where($map)->delete();
        if($result !== false){
            Log::write($log,Log::INFO,'',$this->logName);
            $this->ajaxReturn(['status'=>'1','msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>'0','msg'=>'操作失败']);
        }
    }

    /**
     * 移动规则
     * @author Shawn
     * @date 2018/12/27
     */
    public function moveRule(){


        $id = I('id');	//要移动的规则id
        $sort = I('sort');
        $target_id = I('target_id'); //目标id

        $weightingModel = new WeightingKeywordModel();

        //判断目标id是否存在
        $condition['id'] = $target_id;
        $data = $weightingModel->where($condition)->find();
        if(empty($data)){
            $this->ajaxReturn(['status'=>'0','msg'=>'输入的ID不存在！请重新输入！']);
        }

        //规则排序移动两种情况：
        //1、向上移动：从第一条开始，sort修改为下一条规则的的sort，最后一条修改为第一条规则的sort
        //2、向下移动：从第二条开始，sort修改为上一条规则的的sort，第一条修改为倒数第二条规则的sort(查询条件已去除最后一条)
        $target_sort = $data['sort'];	//目标sort
        $where = array();
        if($sort < $target_sort){	//向下移动
            $where['sort'] = array(
                array('egt', $sort),
                array('lt', $target_sort),
            );
        }else{						//向上移动
            $where['sort'] = array(
                array('egt', $target_sort),
                array('elt', $sort),
            );
        }
        $order = ' sort asc,id asc';
        $result = $weightingModel->where($where)->order($order)->select();
        $count = count($result);

        if($count == 1){
            $this->ajaxReturn(['status'=>'0','msg'=>'无需移动！']);
        }

        //判断移动方向
        if($result[0]['id'] == $id){	//向下移动

            foreach ($result as $key => $value) {
                //如果是第一条，修改为倒数第二条规则的sort（最后一条已去掉）
                if($value == $result[0]){
                    $update['sort'] = $result[$count-1]['sort'];
                }else{
                    $update['sort'] = $result[$key-1]['sort'];
                }
                $flag = $weightingModel->where(array('id'=>$value['id']))->save($update);
                if(!$flag){
                    $this->ajaxReturn(['status'=>'0','msg'=>'规则移动失败！']);
                }
            }

        }elseif ($result[$count-1]['id'] == $id) {	//向上移动

            foreach ($result as $key => $value) {
                //如果是最后一个，sort为第一个的sort
                if($value == $result[$count-1]){
                    $update['sort'] = $target_sort;
                }else{
                    $update['sort'] = $result[$key+1]['sort'];
                }
                $flag = $weightingModel->where(array('id'=>$value['id']))->save($update);
                if(!$flag){
                    $this->ajaxReturn(['status'=>'0','msg'=>'规则移动失败！']);
                }
            }

        }else{
            $this->ajaxReturn(['status'=>'0','msg'=>'程序出错！']);
        }
        $log = '操作人：'.session("truename").PHP_EOL;
        $log .= "移动了称重加权规则，将规则id={$id}移动到了id={$target_id}之前。";
        Log::write($log,Log::INFO,'',$this->logName);
        $this->ajaxReturn(['status'=>'1','msg'=>'规则移动成功！']);
        die;
    }

    /**
     * 上移/下移
     * @author Shawn
     * @date 2018/12/27
     */
    public function altRuleSort(){

        if(!I('id') || I('type') == ''){
            $this->ajaxReturn(['status'=>'0','msg'=>'提交的数据不正确！']);
        }
        $weightingModel = new WeightingKeywordModel();
        $id = I('id');
        $type = I('type');
        $sort = $weightingModel->where(array('id'=>$id))->getField('sort');

        //取得要交换的数据(判断是上移还是下移)
        if($type == 'prev'){
            //上移：取得sort小于自己的第一条记录，进行交换
            $order = 'sort desc,id desc';
            $field = 'id,sort';
            $where['sort'] = array('lt', $sort);
            $data = $weightingModel->field($field)->where($where)->order($order)->limit(1)->select();
        }else{
            //下移：取得sort大于自己的第一条记录，进行交换
            $order = 'sort asc,id asc';
            $field = 'id,sort';
            $where['sort'] = array('gt', $sort);
            $data = $weightingModel->field($field)->where($where)->order($order)->limit(1)->select();
        }

        //判断是否为空（当前记录已经是第一条或者最后一条）
        if(count($data) == 0){
            $this->ajaxReturn(['status'=>'0','msg'=>'当前记录已经是第一条/最后一条！']);
        }

        //交换sort
        $alt_id = $data[0]['id'];
        $alt_sort = $data[0]['sort'];
        $flag_1 = $weightingModel->where(array('id'=>$alt_id))->save(array('sort'=>$sort));
        $flag_2 = $weightingModel->where(array('id'=>$id))->save(array('sort'=>$alt_sort));

        if(!$flag_1 || !$flag_2){
            $this->ajaxReturn(['status'=>'0','msg'=>'修改失败！']);
        }
        $log = '操作人：'.session("truename").PHP_EOL;
        $log .= "用户修改了称重加权规则的优先级：id={$id} 和 id={$alt_id} 的优先级进行了交换。";
        Log::write($log,Log::INFO,'',$this->logName);
        $this->ajaxReturn(['status'=>'1','msg'=>'规则移动成功！']);
    }



}