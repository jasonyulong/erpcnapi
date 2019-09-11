<?php
namespace Task\Model;
use Think\Model\AdvModel;

class EbayHandleModel extends AdvModel{
    
    protected $tableName = 'ebay_onhandle';

    //朱诗萌 2017/11/2 暂时使用erp的存库，以后要调整

    protected $autoCheckFields = false;

    protected $ancestorId = 0;
    
    /**
    * 分表规则
    * @var array
    */
    protected $partition = array(
        'field' => 'store_id',
        'type' => 'fixed',
        'expr' => '`1`',
        'num' => '2',
    );
}