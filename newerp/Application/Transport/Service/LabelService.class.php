<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/10
 * Time: 20:40
 */
/**
*测试人员谭 2018-07-10 20:41:01
*说明: 标签服务
*/
namespace Transport\Service;

use Order\Model\OrderTypeModel;

class LabelService {
	
	private $redis=null;
	
	private $maxlen=2000; //队列最多多少个
	
	private $onceCount=30; //一次运行多少个
	
	private $errCount=3; // 最多重试的次数
	
	
	private $CARRIER_TEMPT_15=[];// 10*15
	private $CARRIER_TEMPLATE_PDF_PATH=[];//有pdf 的运输方式是什么鬼 方便
	private $PNG_LABLE=[];//下载下来就是png 既不是自画，又不是 pdf 这种运输方式


    // /usr/local/redis/bin/redis-cli
    //llen order:checklable

	private $key='order:checklable';
	
	public function __construct(){

        $redisConfig=['host' => C('REDIS_HOST'), 'port' => C('REDIS_PORT'), 'database' => 0];

        print_r($redisConfig);

        $this->redis                     = new Redis($redisConfig);
        $this->CARRIER_TEMPT_15          = C('CARRIER_TEMPT_15');
        $this->CARRIER_TEMPLATE_PDF_PATH = C('CARRIER_TEMPLATE_PDF_PATH');
        $this->PNG_LABLE                 = C('PNG_LABLE');
	}
	
	/**
	*测试人员谭 2018-07-10 21:44:12
	*说明: 需要生成那啥 添加到队列中去
	*/
	public function addList(){

        /**
        *测试人员谭 2018-08-08 11:30:42
        *说明: 找出所有的 have_lable=0 且错误次数 没有达到 x 的订单 去加入队列
        */
		$Orders=$this->_getNeedCheckLables();
		
		foreach ($Orders as $List){
			
			$List['ebay_carrier'] = trim($List['ebay_carrier']);


            /**
             *测试人员谭 2019-01-03 16:00:00
             *说明: 特殊的物流方式，面单是png 但是又需要 套用pdf 的面单的一些 自动程序的一些属性。 这个时候，类似于 字画面单，不需要转化 pdf 。
             *     erp 已经做了 png是否请求成功的判断，才会到wms ，所以这里直接干成 have_lavle =1
             */
            if(in_array($List['ebay_carrier'],$this->PNG_LABLE)){
                $this->_setLableSuccess($List['ebay_id']);
                continue;
            }



            // 如果是自画面单 就直接干成have_lable
            if(!array_key_exists($List['ebay_carrier'],$this->CARRIER_TEMPLATE_PDF_PATH)){

                $this->_setLableSuccess($List['ebay_id']);
                continue;
            }


			$rs=$this->addOneOrder($List);
            //队列
            if(-100==$rs){
                return false;
            }
		} // END For


	}
	

    /**
    *测试人员谭 2018-08-09 10:16:01
    *说明: 测试方法
    */
    public function testConvert(){
        $Orders=array(
            'ebay_id'=>18111207,
            'ebay_carrier'=>'欧速通英国专线挂号',
        );
        $this->_convertPdf2Png($Orders);
    }
	/**
	*测试人员谭 2018-07-10 22:34:37
	*说明: 从队列里面干出一些订单来创建订单
	*/
	public function popOrderCheckLables(){
		$queuekey=$this->key;
		$len = $this->redis->client->Llen($queuekey);
		if ($len <= 0) {
			echo '队列都干完了';
			return false;
		}
		
		$onceCount=$this->onceCount;
		// 每次执行10单
		for ($i = 0; $i < $onceCount; $i++) {
			
			$pop = $this->redis->client->BRPOP($queuekey, 1);
			
			if (empty($pop) || !is_array($pop)) {
				
				echo "none value in {$queuekey} \n";

                print_r($pop);

				return false;
			}
			$queue  = json_decode($pop[1], true);
			
			/**
			*测试人员谭 2018-07-10 22:42:48
			*说明: 提前把img 转化了
			*/

            $ebay_id=$queue['ebay_id'];


            print_r($queue);

			$data=$this->_convertPdf2Png($queue);

            echo 'return:';
            print_r($data);
            print_r("\n");

            if(!$data['status']){
                $this->_setLableFailed($ebay_id); // 失败了啦
                continue;
            }


            $jsonfile=$data['jsonfile'];

            $this->_setLableSuccess($ebay_id,$jsonfile);  // 设置订单成功了

		}
	}
	
	/**
	*测试人员谭 2018-07-10 20:56:57
	*说明: 统一创建面单
	*/
	private function _getNeedCheckLables(){
		 
		/**
		*测试人员谭 2018-07-10 21:31:42
		*说明: 查找 没有生成面单的订单
		*/
		$map['a.ebay_id']=['gt',17000000];
		$map['a.have_lable']=0;
		$map['a.err_count']=['lt',$this->errCount];
		$map['b.ebay_status']=['in',[1723,1724,1745,2009]];

		$orderTypeModel = new OrderTypeModel();
		
		$limit =5000;
		$Orders=$orderTypeModel->alias('a')->join("erp_ebay_order b using(ebay_id)")
			->where($map)->field('b.ebay_id,b.ebay_carrier')
			->order('a.ebay_id asc')
			->limit($limit)
			->select();
		
		echo $orderTypeModel->_sql()."\n\n";
		
		return $Orders;
		
	}
	
	
	/**
	*测试人员谭 2018-07-10 22:28:28
	*说明: 把一个订单干入队列中
	*/
	private function addOneOrder($List){
		$queuekey=$this->key;
		/**
		 *测试人员谭 2018-07-10 22:18:15
		 *说明: 不能把队列干爆了
		 */
		if ($this->redis->client->Llen($queuekey) >= $this->maxlen) {
            echo '队列已经爆满!!!'."\n";
			return -100;
		}
		
		$queueval=json_encode($List);
		
		$lrange   = $this->redis->client->LRANGE($queuekey, 0, -1);
		if (!empty($lrange) && in_array($queueval, $lrange)) {
			echo $List['ebay_id'].'--队列中存在鸟'."\n";
			return false;
		}
		
		return $this->redis->client->LPUSH($queuekey, $queueval);
	}
	


    /**
    *测试人员谭 2018-08-08 11:33:14
    *说明: 转换Pdf 成为 png
    */
    private function _convertPdf2Png(array $Order) {
        $ebay_id      = $Order['ebay_id'];
        $ebay_carrier = $Order['ebay_carrier'];
        $webRoot      = dirname(dirname(THINK_PATH));
        $path         = $this->CARRIER_TEMPLATE_PDF_PATH[$ebay_carrier];

        $file = $webRoot . '/log/lables/' . date('YmdH') . '.txt';

        if (empty($path)) {
            $log = $ebay_id . '---' . $ebay_carrier . '---,配置发生了变化 居然不是pdf了--' . date('ymdHis') . "\n";
            writeFile($file, $log);
            return false;
        }

        $imgUrl    = 'http://erpcnapi.com:8081/erpcnapi/pdf2img/';
        $sourceURL = 'http://hkerp.wisstone.com/';

        $pdfFile = $path . $ebay_id . '.pdf';


        //开始下载pdf 文件到仓库系统
        $sourcePdf = $sourceURL . $pdfFile;

        $data = ['status' => 1, 'msg' => '', 'data' => ''];

        $tempPdffile = $webRoot . '/pdf2img/temppdf/' . $ebay_id . '.pdf';

        //print_r($sourcePdf."\n");
        //print_r($tempPdffile."\n");

        if (!copy($sourcePdf . '?t=' . time() . rand(1111, 9999), $tempPdffile)) {
            $data['status'] = 0;
            $data['msg']    = 'PDF获取失败' . $sourcePdf;
            return $data;
        }


        $isSLS = false;
        if (strstr($path, 'SLS/')) {
            $isSLS = true;
        }

        $dir    = date('ymd');
        $Return = $this->pdf2png($tempPdffile, $isSLS, $dir,$ebay_id);

        echo "转化结果:\n";
        print_r($Return);
        echo "\n";

        $data=[];
        if ($Return != false || is_array($Return)) {
            foreach ($Return as $list) {
                $data['data'][] = $imgUrl . $list;
            }
            $data['jsonfile'] = 'pdf2img/pngLable/' . $dir . '/' . $ebay_id . '.json';
            $data['status'] = 1;
        } else {
            $data['status']   = 0;
            $data['msg']      = 'PDF转化失败';
            $data['jsonfile'] = '';
        }


        /**
        *测试人员谭 2018-08-08 16:17:51
        *说明: 吧这个成功的结果返回  再干到文件中
        */
        $json = json_encode($data);

        if (!empty($data['jsonfile'])) {
            // 写入文件时 独占锁定
            file_put_contents($webRoot . '/' . $data['jsonfile'], $json,LOCK_EX);
        }

        return $data;

    }


    /**
     *  $sls 这个特殊玩意 非常的神奇，pdf 转化之后 会出现 有效区域不在 左上角，需要分析图片，找到 第一条黑线在 图片画布的 什么位置
     *  $PDF PDF玩意的绝对路径
     *  $dir 文件夹 时间名称 为了避免比较极端的 凌晨问题，让调用者 去解决吧
     */
    private  function pdf2png($PDF,$sls,$dir,$ebay_id){

        if(!file_exists($PDF)){
            echo 'not file';
            return false;
        }

        $Path=dirname(dirname(THINK_PATH)).'/pdf2img/pngLable';

        //$bname=basename($PDF);

        $IM =new \imagick();
        $IM->setResolution(300,300);
        $IM->setCompressionQuality(100);

        try{
            $IM->readImage($PDF);
        }catch(\Exception $e){
            echo '<pre>';
            print_r($e);
            return false;
        }


        $Return=[];
        $i=0;

        if(!file_exists($Path.'/'.$dir)){
            mkdir($Path.'/'.$dir, 0777, true);
            chmod($Path.'/'.$dir,0777);
        }

        #print_r($IM);

        #print_r($IM);
        foreach($IM as $Key => $Var){
            $filename=date('YmdHis').$ebay_id.(10000*(microtime(true))).rand(1,99999);

            $Var->setImageFormat('png');
            $Filename = $Path.'/'.$dir.'/'.$filename.'.png';
            if(file_exists($Filename)){
                unlink($Filename);
            }

            if($Var->writeImage($Filename)==true){
                if($sls){
                    $height=$this->_getImgcolor($Filename);
                    $Filename.='?height='.$height;
                }
                $Return[]= $Filename;
            }
            $i++;
        }

        return $Return;



    }

    private  function _getImgcolor($file){
        $im = imagecreatefrompng($file);
        /**
         *测试人员谭 2017-12-09 13:58:01
         *说明: 第一次出现黑色的 宽度是什么
         */
        for($i=40;$i<130;$i++){
            $rgb = imagecolorat($im,200 , $i);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            // print_r([$r,$g,$b]);
            if($r<=2&&$g<=2&&$b<=2){
                //echo $i.'<br>';
                return $i;
            }
        }
        return 0;
    }



    /**
    *测试人员谭 2018-08-08 11:38:49
    *说明: 把面单状态 干成ok !
    */
    private function _setLableSuccess($ebay_id,$lable_path=''){
        $orderTypeModel = new OrderTypeModel();
        $save['have_lable']=1;

        if($lable_path){
            $save['lable_path']=$lable_path;
        }
        $orderTypeModel->where(compact('ebay_id'))->limit(1)->save($save);
        @unlink(dirname(dirname(THINK_PATH)). '/pdf2img/temppdf/' . $ebay_id . '.pdf');

    }


    /**
    *测试人员谭 2018-08-08 16:23:57
    *说明: 设置失败的玩意 就是说 如果失败了 咱就把这个 err_count ++
    */

    private function _setLableFailed($ebay_id){
        $orderTypeModel = new OrderTypeModel();
        $orderTypeModel->where(compact('ebay_id'))->limit(1)->setInc('err_count');
    }
	
}