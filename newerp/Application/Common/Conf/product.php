<?php
return array(
    'FLOW_STEPS' => array(
        '0' => '待初审',
        '1' => '待开发',
        // '2'  => '待审批',
        '3' => '待询价',
        '4' => '待下样品单',
        '5' => '待取样',
        '6' => '待拍图',
        '7' => '待完善',
        '8' => '待美工处理',
        '9' => '待终审',
        '10' => '已完成',
        '11' => '待完善商品属性',
        '12' => '待处理文案编辑',
        '13' => '待上架',
        '14' => '已上架',
        '15' => '已下架',
        '16' => '待完善图片信息',
        '-1' => '已终止',
        '-2' => '已退回',
    ),
    //库位配置
    'GOODS_STATUS' => array(
        'S' => '微型库位',
        'M' => '小型库位',
        'L' => '中型库位',
        'XL' => '大型库位',
        'XXL' => '超大型库位'
    ),
    //带包装且发货去包装
    'IS_NO_PACKAGING' => array(
        '0' => '未设定',
        '1' => '是',
        '2' => '否'
    ),
    //产品类型
    'GOODS_TYPE' => array(
        '0' => '普通产品',
        '1' => '多属性产品',
        '2' => '多属性单品'
    ),
    //淘汰列表产品类型
    'GOODS_OUT_TYPE' => array(
        '0' => '普通产品',
        '1' => '多属性产品'
    ),

    //询价区间
    'ASK_PRICE_SECTION' => array(
        '0' => '1-5',
        '1' => '6-10',
        '2' => '11-50',
        '3' => '51-100',
        '4' => '101-500'
    ),
    //采购单状态
    'PURCHASE_ORDER_STATUS' => array(
        '1' => '等待付款',
        '2' => '已经付款等待收货',
        '3' => '已经到货等待处理',
        '4' => '等待仓库收货',
        '5' => '等待入库',
        '6' => '完结',
        '7' => '无法完结',
        '8' => '放弃采购'
    ),
    //所有平台
    'ALL_PLATFORM' => array(
        array('title' => 'ebay', 'value' => 'ebay'),
        array('title' => 'aliexpress', 'value' => 'aliexpress'),
        array('title' => 'wish', 'value' => 'wish'),
        array('title' => 'cdiscount', 'value' => 'cdiscount'),
        array('title' => 'priceminister', 'value' => 'priceminister'),
        array('title' => 'lazada', 'value' => 'lazada'),
        array('title' => 'joom', 'value' => 'joom'),
        array('title' => 'linio', 'value' => 'linio'),
        array('title' => 'shopee', 'value' => 'shopee'),
        array('title' => 'walmart', 'value' =>  'walmart'),
        array('title' => 'amazon', 'value' => 'amazon'),
    ),
    'isPacking' => array(
        0 		=> '带包装',
        1		=> '裸装',
        2		=> '带OPP袋'
    ),
);
