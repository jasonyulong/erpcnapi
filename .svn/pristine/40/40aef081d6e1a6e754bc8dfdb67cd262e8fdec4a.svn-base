<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/16
 * Time: 13:50
 */
namespace Order\Controller;

use Common\Model\EbayOnhandleModel;
use Order\Model\PickAbnormalitySkuModel;
use Package\Service\CreateSingleOrderService;
use Think\Controller;

class PickingAbnormalitySkuController extends Controller
{

    /**
     * @var string MP3音频地址
     */
    private $getMp3Url = 'http://tts.baidu.com/text2audio?lan=zh&ie=UTF-8&spd=6&text=';

    /**
     * @desc 页面展示
     * @Author leo
     */
    public function index()
    {
        if (!can('picking_abnormality_sku')) {
            echo "<h1 style='color: red'> 您没有相关权限！ </h1>";
            exit;
        }

        $abnormalitySkuModel = new PickAbnormalitySkuModel();
        $between[] = strtotime(date('Y-m-d'));
        $between[] = strtotime(date('Y-m-d') . " 23:59:59");
        $map['addtime'] = ['between', $between];
        $toDayCount = $abnormalitySkuModel->field("COUNT(DISTINCT sku) as skucount,sum(qty) as qtycount")->where($map)->find();
        $datalist = $abnormalitySkuModel->field('sku,qty,picker')->where($map)->order('addtime desc')->limit(20)->select();

        $store_id            = C('CURRENT_STORE_ID');
        $onhandleModel       = new EbayOnhandleModel($store_id);

        foreach($datalist as $key=>$val){
            $where['goods_sn']   = $val['sku'];
            $is_on               = $onhandleModel->field('g_location')->where($where)->find();
            $datalist[$key]['g_location'] = $is_on['g_location'];
        }
        $this->assign('toDayCount', $toDayCount);
        $this->assign('datalist', $datalist);
        $this->display();
    }

    /**
     * @desc 保存sku的扫描记录
     * @Author leo
     */
    public function saveAbnormalitySku()
    {
        $abnormalitySkuModel = new PickAbnormalitySkuModel();
        $store_id            = C('CURRENT_STORE_ID');
        $onhandleModel       = new EbayOnhandleModel($store_id);
        $sku                 = trim($_POST['sku']);
        $where['goods_sn']   = $sku;
        $pickServer          = new CreateSingleOrderService();
        $is_on               = $onhandleModel->field('g_location')->where($where)->find();

        $between[] = strtotime(date('Y-m-d'));
        $between[] = strtotime(date('Y-m-d') . " 23:59:59");
        $map['addtime'] = ['between', $between];
        $map['sku'] = $sku;

        if ($is_on) {
            $picker   = $pickServer->getPicker($is_on['g_location']);

            $this->saveMp3($picker);

            $addData  = [
                'sku'     => $sku,
                'picker'  => $picker,
                'addtime' => time(),
                'adduser' => session('truename'),
                'qty'     => 1,
            ];
            $map['picker'] = $picker;
            $is_id    = $abnormalitySkuModel->field('id,qty')->where($map)->find();
            $newsku = 0;
            if($is_id){
                $newQty = $is_id['qty']+1;
                $saveData  = [
                    'qty' => $newQty,
                    'addtime' => time(),
                ];
                $res      = $abnormalitySkuModel->where(['id'=>$is_id['id']])->save($saveData);
            }else{
                $newQty = 1;
                $newsku = 1;
                $res      = $abnormalitySkuModel->add($addData);
            }
            $jsonData = ['status' => 1, 'sku' => $sku, 'picker' => $picker,'qty'=>$newQty,'newsku'=>$newsku,'location'=>$is_on['g_location']];
            if (!$res) {
                $msg      = "扫描异常";
                $jsonData = ['status' => 0, 'msg' => $msg];
            }
        } else {
            $msg      = '未找到sku记录';
            $jsonData = ['status' => 0, 'msg' => $msg];
        }

        $this->ajaxReturn($jsonData);
    }

    /**
     * @desc 保存mp3
     * @Author leo
     */
    public function saveMp3($picker)
    {
        $fileDir   =dirname(dirname(THINK_PATH)).'/capi/number/';
        if (!file_exists($fileDir)) {
            mkdir($fileDir, 0777, true);
        }
        $file   = $fileDir.$picker.'.mp3';
        $file = $this->convertEncoding($file);
        if(is_file($file)){
            return false;
        }

        $mp3Body = @file_get_contents($this->getMp3Url.$picker);
        if(strlen($mp3Body)<100){
            $mp3Body=@file_get_contents($this->getMp3Url.$picker);
        }

        if(strlen($mp3Body)<100){
            return false;
        }

        file_put_contents($file,$mp3Body);
        chmod($file,0777);
    }

    /**
     * 转换字符编码
     * @param $string
     * @return string
     */
    function convertEncoding($string){
        //根据系统进行配置
        $encode = stristr(PHP_OS, 'WIN') ? 'GBK' : 'UTF-8';
        $string = iconv('UTF-8', $encode, $string);
        //$string = mb_convert_encoding($string, $encode, 'UTF-8');
        return $string;
    }

}



