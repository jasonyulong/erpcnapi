<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5
 * Time: 20:51
 */

namespace Package\Controller;


use Package\Model\PickLocationModel;
use Think\Controller;

/**
 |-------------------------------------------------------------------------------------
  三楼 的location
 * Class CalcPackageFeeController
 * @package Package\Controller
 */
class LocationController extends Controller{

    function viewLocation(){
        $PickLocation=new PickLocationModel();
        $Locations=$PickLocation->find();

        $this->assign('Locations',$Locations);
        $this->assign('id',(int)$Locations['id']);
        $this->display('location');
    }


    function saveLocation(){
        $id       = (int)$_POST['id'];
        $location = $_POST['location'];

        $location=str_replace('，',',',$location);
        $location=str_replace(' ','',$location);
        $PickLocation=new PickLocationModel();
        $save['location']=$location;
        if($id==0){
            $rr=$PickLocation->add($save);
        }else{
            $rr=$PickLocation->where(['id'=>$id])->save($save);
        }

        if($rr!==false){
            echo json_encode(['msg'=>'修改成功','status'=>1]);
        }else{
            echo json_encode(['msg'=>'修改失败','status'=>0]);
        }

    }

    //http://www.cnapi.cn/t.php?s=/Package/Location/printLocation
    //
    function printLocation(){
        $Arr=['A','B','C','D','E','F','G','H','J','K','L'];//[,'M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];


        $ss=[];

        //foreach($Arr as $List){

            for($i=1;$i<=90;$i++){
                $ss[]=$i;
            }


        echo '<meta charset="utf-8"><style>*{padding:0;margin:0}</style>';


        foreach($ss as $List){
            echo '<div style="width:10cm;height:10cm;line-height:10cm;border: 1px #000 dotted;text-align:center;font-size:200px;">'.$List.'</div>';
        }

        $this->assign('Data',$ss);

    }
}