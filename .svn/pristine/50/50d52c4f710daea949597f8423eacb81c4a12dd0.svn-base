<?php
/**
 * User: 王模刚
 * Date: 2017/10/26
 * Time: 20:15
 */

namespace Mid\Controller;


use Think\Controller;

class GetEbayOnhandleController extends Controller
{
    /**
     * mid_ebay_onhandle_196 同步到 erp_ebay_onhandle_196 此方法现在没有使用
     * @author 王模刚
     * @since  2017 10 26
     * @link  http://local.erpanapi.com/t.php?s=/Mid/GetEbayOnhandle/GetEbayOnhandle
     */
    public function GetEbayOnhandle(){
        ini_set('memory_limit','1024M');
        $handleService = new \Mid\Service\EbayHandleService();
        $request = $_REQUEST;
        $handleService->getOnhandle($request);
        echo "\n\n\n".'End';
    }
}