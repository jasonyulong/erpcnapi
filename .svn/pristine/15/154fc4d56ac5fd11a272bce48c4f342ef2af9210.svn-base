<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name RedisPanelController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2019/4/18
 * @Time: 10:08
 * @Description redis 面板
 */
namespace Test\Controller;

use Think\Cache\Driver\Redis;
use Think\Controller;
class RedisPanelController extends Controller
{

    public function index()
    {
        $tableConfig = C("NEED_SYNC_TABLE_ARR");
        $redisConfig = C("ERP_REDIS_CONFIG");
        $dbId        = C("REDIS_DATABASE_ID");
        $currStoreId = C("CURRENT_STORE_ID");
        $data        = array();
        $key         = "need_sync_data:";
        $redis       = new Redis($redisConfig);
        $redis->select($dbId);
        foreach ($tableConfig as $k=>$value){
            $redisKey   = $key.$k.':'.$currStoreId;
            $len        = 0;
            $tempData   = array();
            if($redis->exists($redisKey)){
                $len = $redis->Llen($redisKey);
            }
            $data[$redisKey]['length']  = $len;
            $data[$redisKey]['data']    = $tempData;
        }
        $this->assign("data",$data);
        $this->display();
    }

    /**
     * @desc 一次性任务 刷新数据
     * @author Shawn
     * @date 2019/4/30
     */
    public function refreshGoodsData()
    {
        if(!IS_CGI || APP_ENV != 'development'){
            p("must run cli");
            exit;
        }
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1200M');
        $erpTableModel = M("ebay_goods",'','DB_CONFIG_ERP_READ');
        $wmsTableModel = M("ebay_goods",'','DB_CONFIG_READ');
        $wmsModel = M("ebay_goods");
        $count = $wmsTableModel->count();
        $count = (int)$count;
        if($count == 0){
            p("数据为空");
            exit;
        }
        $limit = 5000;
        $page = ceil($count/$limit);
        p("分页数：".$page.",每页：".$limit);
        for($i=1;$i<=$page;$i++){
            p("页码：".$i."开始");
            $begin = ($i - 1)*$limit;
            $goodsData = $wmsTableModel
                ->limit("{$begin},{$limit}")
                ->order("goods_id asc")
                ->getField("goods_id,labletype",true);
            if(empty($goodsData)){
                p("页码:".$i."结束，数据为空");
                continue;
            }
            foreach ($goodsData as $k=>$v){
                $map['goods_id'] = $k;
                $lableType = $erpTableModel->where($map)->getField("labletype");
                if(empty($lableType)){
                    p("erp商品信息为空，goods_id:".$v);
                    continue;
                }
                if($v == $lableType){
                    p("goods_id:".$k."数据一致，无须刷新");
                    continue;
                }
                $result = $wmsModel->where($map)->save(['labletype'=>$lableType]);
            }
            p("页码：".$i."结束");
        }
        exit("done");
    }


}