<?php
/**
*测试人员谭 2017-09-12 19:27:29
*说明: 仓库白单
*/

/**
*测试人员谭 2018-05-17 21:47:27
*说明: url 这里 增加 两个配置
 * 'url'=>'xxx'         // 重新获取PDF 的链接
 * 'regetCount'=>2,     // 重新获取PDF 错误的次数  在程序里 默认是 1 次
 * 'after'=>1           // 在第一次 获取失败之后，下一次什么时候获取 ？ 这个配置适合有些物流必须要申请 跟踪号 之后一段时间之后 才能获取到, 例如第一次获取失败后的 10min 填 600 ！ 不设置 默认是1 也就是可立即获取
*/
return array(
    'PDF_CARRIER'=>array(
        'JOOM线上东莞挂号' => ['url' => 'http://erp.wst/t.php?s=/Transport/Joom/getLable/id/'],
        'JOOM线上东莞平邮' => ['url' => 'http://erp.wst/t.php?s=/Transport/Joom/getLable/id/'],
            '比利时平邮-HYD' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '中欧专线平邮-ebay' => ['url'=>'http://erp.wst/t.php?s=/Transport/YunTuCarrierApi/multiGetPdf/ordersn/'], //云途
            'wish线上广州eub带电-AJ' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上武汉eub-AJ' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        '美国虚拟仓普货大包-TJ' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
        '美国虚拟仓内电大包-TJ' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
        '美国虚拟仓特货大包-TJ' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
        '美国虚拟仓特货-TJ' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
        '美国虚拟仓内电-TJ' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
        '美国虚拟仓普货-TJ' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            'wish邮广州挂号-WYT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish邮广州平邮-WYT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            '速卖通线上挂号-HYT' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
            '速卖通线上平邮-HYT' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
            'wish邮成都挂号-HYT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish邮成都平邮-HYT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        '印度专线-QHY' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '联邮通英国专线挂号特惠' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '新加坡小包平邮' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '新加坡小包挂号' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            'wish香港平邮-JHD' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            'wish邮线上美国专线-SF' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'Wish线上ubi加拿大特惠专线' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            '线下EUB广州-YLD' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '线下EUB广州(带电)-YLD' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '俄速通小包挂号' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '顺丰东欧小包挂号' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '美国专线-SF' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '欧洲专线平邮-FT'  =>  ['url' => 'http://erp.wst/t.php?s=/Transport/Feite/batchOrderAction/type/print/ordersn/'],
            'wish邮线上香港小包挂号-DSF' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish邮线上香港小包平邮-DSF' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上英国联邮通挂号特惠-DSF' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上新邮挂号-DSF' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上新邮微包平邮-DSF' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'JOOM-RU经济' => ['url' => 'http://erp.wst/t.php?s=/Transport/Joom/getLable/id/'],
            'wish线上UBI比利时平邮' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上UBI墨西哥SCM专线挂号' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上UBI澳邮专线挂号' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上UBI加拿大专线挂号' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上UBI西班牙专线平邮' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish航空挂号小包特货-YW' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish专线追踪小包特货-YW' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish专线平邮小包特货-YW' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish法国专线挂号特惠-YT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        '速卖通线上俄罗斯菜鸟特货专线-简易' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上菜鸟特货专线-超级经济'   => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
            'wish线上新加坡挂号' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上新加坡平邮' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            '澳大利亚专线-YD' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
        'Wish邮广州挂号'       => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'Wish邮广州平邮'       => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        '速卖通线上挂号-广州仓' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上平邮-广州仓' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
            '荷邮纯电挂号-YD' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '柬埔寨平邮-YD' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '荷邮纯电平邮-YD' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            '德国专线-YD' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
            'Wsh线上EUB带电-HYD' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'Wsh线上EUB-HYD'     => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            '顺邮宝挂号-SY' => ['url'=>'http://erp.wst/t.php?s=/Transport/ShunYou/batchOrderAction&type=print&ordersn='],
            'Wish线上香港挂号-YT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'Wish线上快速小包平邮-YT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'Wish线上香港平邮-YT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'Wish线上中美专线（特惠）-YT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'Wish线上中欧专线挂号-YT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'Wish线上中欧专线平邮-YT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'Wish线上西班牙特惠专线平邮-YT' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上航空普货-YW'   => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上航空特货-YW'   => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'wish线上燕邮宝-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'Wish线上顺邮宝平邮-SY' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
            'Wish线上顺邮宝挂号-SY' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        '荷兰邮政小包挂号'       => ['url' => 'http://erp.wst/t.php?s=/Transport/OfflineCarrierGetTracknum/oldWenHuiGetPdf/orderid/',],
        '俄罗斯小包WH'        => ['url' => 'http://erp.wst/t.php?s=/Transport/OfflineCarrierGetTracknum/oldWenHuiGetPdf/orderid/',],
        '荷兰邮政小包平邮'       => ['url' => 'http://erp.wst/t.php?s=/Transport/OfflineCarrierGetTracknum/oldWenHuiGetPdf/orderid/','regetCount' => 3],
        '新疆小包挂号-WH'       => ['url' => 'http://erp.wst/t.php?s=/Transport/OfflineCarrierGetTracknum/oldWenHuiGetPdf/orderid/',],
        // hank 2018/3/15 9:48
        '比邮挂号-WH'       => ['url' => 'http://erp.wst/t.php?s=/Transport/OfflineCarrierGetTracknum/oldWenHuiGetPdf/orderid/',],
        '比邮平邮-WH'       => ['url' => 'http://erp.wst/t.php?s=/Transport/OfflineCarrierGetTracknum/oldWenHuiGetPdf/orderid/',],
        '比邮挂号26国-WH'   => ['url' => 'http://erp.wst/t.php?s=/Transport/OfflineCarrierGetTracknum/oldWenHuiGetPdf/orderid/',],
        // hank 2018/4/2 14:24
        '新疆EUB-WH'   => ['url' => 'http://erp.wst/t.php?s=/Transport/OfflineCarrierGetTracknum/oldWenHuiGetPdf/orderid/',],
        '新疆中邮挂号-WH' => ['url' => 'http://erp.wst/t.php?s=/Transport/OfflineCarrierGetTracknum/oldWenHuiGetPdf/orderid/',],

        '欧速通'            => ['url' => 'http://erp.wst/t.php?s=/Transport/OST/multiGetOrderPdf/bills/'],
        '欧速通英国专线挂号'  => ['url' => 'http://erp.wst/t.php?s=/Transport/OST/multiGetOrderPdf/bills/'],
        '欧速通（带电）'  => ['url' => 'http://erp.wst/t.php?s=/Transport/OST/multiGetOrderPdf/bills/'],
        '欧速通英国专线挂号(带电)'  => ['url' => 'http://erp.wst/t.php?s=/Transport/OST/multiGetOrderPdf/bills/'],
        '卢森堡挂号' => ['url' => 'http://erp.wst/t.php?s=/Transport/OST/multiGetOrderPdf/bills/'],

        '马来西亚平邮易递宝（万邑通）' => ['url' => 'http://erp.wst/t.php?s=/Transport/WinitISP/batchOrderAction/type/print/ordersn/',],
        '香港平邮易递宝（万邑通）'   => ['url' => 'http://erp.wst/t.php?s=/Transport/WinitISP/batchOrderAction/type/print/ordersn/',],
        '线上中邮易递宝（万邑通）'   => ['url' => 'http://erp.wst/t.php?s=/Transport/WinitISP/batchOrderAction/type/print/ordersn/',],
        '线上中邮万邑通-深圳邮局'   => ['url' => 'http://erp.wst/t.php?s=/Transport/WinitISP/batchOrderAction/type/print/ordersn/',],


        //'线下EUB广州(带电)-YLD'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YouLanDa/batchOrderAction/type/print/ordersn/',],
        //'线下EUB广州-YLD'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YouLanDa/batchOrderAction/type/print/ordersn/'],

        //'重庆小包挂号-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        //'重庆小包平邮-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        //'燕邮宝平邮内电-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        //'西邮经济小包-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '土耳其邮政挂号-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '土耳其邮政平邮-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '香港小包挂号-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '香港小包平邮-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '新加坡小包挂号-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '新加坡小包平邮-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '马来西亚小包挂号-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '马来西亚小包平邮-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '荷兰小包挂号-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '荷兰小包平邮-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '比利时小包挂号-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '比利时小包平邮-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        'Wish邮挂号-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        'Wish邮平邮-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '燕邮宝平邮-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '航空经济小包普货-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '航空经济小包特货-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '航空挂号小包普货-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '航空挂号小包特货-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        '英国专线微包-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        // hank 2018/3/15 9:48
        'Wish邮南京挂号-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],
        'Wish邮南京平邮-YW'     => ['url' => 'http://erp.wst/t.php?s=/Transport/YanWen/batchOrderAction/type/print/ordersn/'],


        '法国专线-万欧'     => ['url' => 'http://erp.wst/t.php?s=/Transport/WanOu/batchOrderAction/type/print/ordersn/'],
        'CTAM小包平邮'     => ['url' => 'http://erp.wst/t.php?s=/Transport/Feite/batchOrderAction/type/print/ordersn/','regetCount'=>2,'after'=>1],
        'CTAM小包平邮-普货'     => ['url' => 'http://erp.wst/t.php?s=/Transport/Feite/batchOrderAction/type/print/ordersn/'],
        'CTAM小包挂号'     => ['url' => 'http://erp.wst/t.php?s=/Transport/Feite/batchOrderAction/type/print/ordersn/'],
        'CTAM小包平邮-液体'     => ['url' => 'http://erp.wst/t.php?s=/Transport/Feite/batchOrderAction/type/print/ordersn/'],
        '英国专线平邮-FT' => ['url' => 'http://erp.wst/t.php?s=/Transport/Feite/batchOrderAction/type/print/ordersn/'],
        '意大利专线平邮-FT' => ['url' => 'http://erp.wst/t.php?s=/Transport/Feite/batchOrderAction/type/print/ordersn/'],

        '德国专线-万欧'     => ['url' => 'http://erp.wst/t.php?s=/Transport/WanOu/batchOrderAction/type/print/ordersn/'],
        '意大利专线-万欧'     => ['url' => 'http://erp.wst/t.php?s=/Transport/WanOu/batchOrderAction/type/print/ordersn/'],
        '西班牙专线-万欧'     => ['url' => 'http://erp.wst/t.php?s=/Transport/WanOu/batchOrderAction/type/print/ordersn/'],
        '法国专线标准-WB'     => ['url' => 'http://erp.wst/t.php?s=/Transport/WanOu/batchOrderAction/type/print/ordersn/'],
        //'E速宝'     => ['url' => 'http://erp.wst/t.php?s=/Transport/EMS/batchOrderAction/type/print/ordersn/'],
        '比利时小包'     => ['url' => 'http://erp.wst/t.php?s=/Transport/EMS/batchOrderAction/type/print/ordersn/'],

        '法国专线-安骏'     => ['url' => 'http://erp.wst/t.php?s=/Transport/AnJun/multiGetPdf.html&bills='],
        '德国专线-安骏'     => ['url' => 'http://erp.wst/t.php?s=/Transport/AnJun/multiGetPdf.html&bills='],
        '美国专线-安骏'     => ['url' => 'http://erp.wst/t.php?s=/Transport/AnJun/multiGetPdf.html&bills='],
        '英国直飞专线-安骏'     => ['url' => 'http://erp.wst/t.php?s=/Transport/AnJun/multiGetPdf.html&bills='],

        '广州小包挂号-星邮'     => ['url' => 'http://erp.wst/t.php?s=/Transport/Xingyou/batchOrderAction/type/print/ordersn/'],
        '广州小包平邮-星邮'     => ['url' => 'http://erp.wst/t.php?s=/Transport/Xingyou/batchOrderAction/type/print/ordersn/'],

        '顺邮宝平邮'=>['url'=>'http://erp.wst/t.php?s=/Transport/ShunYou/batchOrderAction/type/print/ordersn/'],
        '顺邮宝挂号'=>['url'=>'http://erp.wst/t.php?s=/Transport/ShunYou/batchOrderAction/type/print/ordersn/'],
        '顺友通平邮'=>['url'=>'http://erp.wst/t.php?s=/Transport/ShunYou/batchOrderAction/type/print/ordersn/'],
        '顺友通挂号'=>['url'=>'http://erp.wst/t.php?s=/Transport/ShunYou/batchOrderAction/type/print/ordersn/'],
        '顺达宝平邮'=>['url'=>'http://erp.wst/t.php?s=/Transport/ShunYou/batchOrderAction/type/print/ordersn/'],
        '顺邮宝平邮2-SY' => ['url'=>'http://erp.wst/t.php?s=/Transport/ShunYou/batchOrderAction&type=print&ordersn='],

        '德国专线平邮'=>['url'=>'http://erp.wst/t.php?s=/Transport/YunTuCarrierApi/multiGetPdf/ordersn/'], //云图
        //'华南小包平邮（快速）'=>['url'=>'http://erp.wst/t.php?s=/Transport/YunTuCarrierApi/multiGetPdf/ordersn/'], //云图
        // hank 2018/1/17 11:42
        '中欧专线平邮'=>['url'=>'http://erp.wst/t.php?s=/Transport/YunTuCarrierApi/multiGetPdf/ordersn/'], //云图
        '中欧特快专线平邮'=>['url'=>'http://erp.wst/t.php?s=/Transport/YunTuCarrierApi/multiGetPdf/ordersn/'], //云图
        // hank 2018/3/17 9:29
        '英国虚拟仓平邮'=>['url'=>'http://erp.wst/t.php?s=/Transport/YunTuCarrierApi/multiGetPdf/ordersn/'], //云图
        '俄罗斯平邮-YT' => ['url'=>'http://erp.wst/t.php?s=/Transport/YunTuCarrierApi/multiGetPdf/ordersn/'], //云图
        '快速小包平邮-YT' => ['url'=>'http://erp.wst/t.php?s=/Transport/YunTuCarrierApi/multiGetPdf/ordersn/'], //云图
        '西班牙特惠专线平邮-YT' => ['url'=>'http://erp.wst/t.php?s=/Transport/YunTuCarrierApi/multiGetPdf/ordersn/'], //云图

        '顺丰国际小包'=>['url'=>'http://erp.wst/t.php?s=/Transport/ShunFeng/multiGetOrderPdf/bills/'],
        '顺丰荷兰小包平邮'=>['url'=>'http://erp.wst/t.php?s=/Transport/ShunFengCbte/multiGetOrderPdf/bills/'],
        '顺丰荷兰小包挂号'=>['url'=>'http://erp.wst/t.php?s=/Transport/ShunFengCbte/multiGetOrderPdf/bills/'],
        '顺丰澳大利亚小包挂号'=>['url'=>'http://erp.wst/t.php?s=/Transport/ShunFengCbte/multiGetOrderPdf/bills/'],


        '出口易小包挂号'=>['url'=>'http://erp.wst/t.php?s=/Transport/ChuKou1/batchOrderAction/type/print/ordersn/'],
        '出口易小包平邮'=>['url'=>'http://erp.wst/t.php?s=/Transport/ChuKou1/batchOrderAction/type/print/ordersn/'],


        '线下EUB(加拿大)鼎立'=>['url'=>'http://erp.wst/t.php?s=/Transport/DingLi/batchOrderAction/type/print/ordersn/'],


        'UBI墨西哥专线'      => ['url' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiGetPdf.html&bills='],
        'UBI澳大利亚平邮（带电）' => ['url' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiGetPdf.html&bills='],
        'UBI澳大利亚平邮'     => ['url' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiGetPdf.html&bills='],
        'UBI俄罗斯专线'      => ['url' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiGetPdf.html&bills='],
        'UBI俄罗斯专线（带电）'  => ['url' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiGetPdf.html&bills='],
        'UBI加拿大专线（带电）'  => ['url' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiGetPdf.html&bills='],
        'UBI墨西哥专线（带电）'  => ['url' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiGetPdf.html&bills='],
        'UBI澳大利亚专线（带电）' => ['url' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiGetPdf.html&bills='],
        'UBI澳大利亚专线'     => ['url' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiGetPdf.html&bills='],
        'UBI加拿大专线'      => ['url' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiGetPdf.html&bills='],
        'UBI欧盟28国-半程查件'      => ['url' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiGetPdf.html&bills='],

        'Wish平邮-广州仓'      => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        // hank 2018/3/30 16:53
        'Wish邮厦门挂号-GD'      => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'Wish邮厦门平邮-GD'     => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'Wish邮厦门挂号带电-GD'  => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'Wish邮厦门平邮带电-GD'  => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'wish线上Fly挂号-FT'    => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'wish线上Fly平邮-FT'    => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'Wish线上老挝平邮'       => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'Wish线上老挝平邮-特货'  => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'Wish邮深圳平邮（带电）' => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'Wish邮深圳平邮'        => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],
        'Wish邮东莞挂号'        => ['url' => 'http://erp.wst/t.php?s=/Transport/WishYou/batchOrderAction/type/print/ordersn/'],

        'DHL小包挂号-德国预清关' => ['url' => 'http://erp.wst/t.php?s=/Transport/DHL/batchOrderAction/type/print/ordersn/'],
        'DHL小包平邮-德国预清关' => ['url' => 'http://erp.wst/t.php?s=/Transport/DHL/batchOrderAction/type/print/ordersn/'],
        'DHl小包挂号'       => ['url' => 'http://erp.wst/t.php?s=/Transport/DHL/batchOrderAction/type/print/ordersn/'],
        'DHl小包挂号(带电)'   => ['url' => 'http://erp.wst/t.php?s=/Transport/DHL/batchOrderAction/type/print/ordersn/'],
        'DHL小包平邮(带电)'   => ['url' => 'http://erp.wst/t.php?s=/Transport/DHL/batchOrderAction/type/print/ordersn/'],
        'DHL小包平邮'   => ['url' => 'http://erp.wst/t.php?s=/Transport/DHL/batchOrderAction/type/print/ordersn/'],
        '美国专线-DHL'   => ['url' => 'http://erp.wst/t.php?s=/Transport/DHL/batchOrderAction/type/print/ordersn/'],
        'DHL经济平邮'   => ['url' => 'http://erp.wst/t.php?s=/Transport/DHL/batchOrderAction/type/print/ordersn/'],
        '德国专线-DHL'  => ['url' => 'http://erp.wst/t.php?s=/Transport/DHL/batchOrderAction/type/print/ordersn/'],

        '速卖通线上小包4PX新邮经济小包'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包无忧标准'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包无忧标准（非普货）'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包无忧简易'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包中外运-西邮经济'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包顺友航空经济小包深圳仓'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包无忧标准-普货'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包云途挂号'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包挂号厦门仓'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包挂号-无锡仓'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上燕文航空经济小包'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        // hank 2018/1/17 18:10
        '速卖通线上小包无忧简易-西班牙'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上顺丰国际经济小包'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        // hank 2018/2/26 12:02
        '速卖通线上顺丰航空经济小包'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上无忧优先'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包挂号-南京仓'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包挂号(鼎立莆田仓)'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包平邮厦门仓'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包平邮-无锡仓'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包平邮-南京仓'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包云途'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包(鼎立通莆田仓)'=> ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上平邮带电-深圳仓' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上无忧集运挂号-SA' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上无忧集运挂号-AE' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上无忧集运平邮-SA' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上无忧集运平邮-AE' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上小包无忧简易-ES' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],
        '速卖通线上菜鸟经济-RU' => ['url' => 'http://erp.wst/t.php?s=/Transport/SMT/multiPdf/ordersn/'],

        'SRM简易挂号-俄罗斯'=> ['url' => 'http://erp.wst/t.php?s=/Transport/GuanDa/batchOrderAction/type/print/ordersn/'],
        'RM挂号-俄罗斯'=> ['url' => 'http://erp.wst/t.php?s=/Transport/GuanDa/batchOrderAction/type/print/ordersn/'],
        // hank 2017/11/17 20:03 添加通邮
        '老挝小包平邮快线-TY'=> ['url' => 'http://erp.wst/t.php/t.php?s=/Transport/TongYou/batchOrderAction/type/print/ordersn/'],
        '老挝小包平邮-TY' => ['url' => 'http://erp.wst/t.php/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
        // hank 2018/1/11 11:13 Linio物流 没有单独的获取面单接口，只能通过创建订单接口重新获取
        'Linio物流'=> ['url' => 'http://erp.wst/t.php?s=/Transport/MailAmericas/batchOrder/type/create/ordersn/'],
        'IB中美专线（标准）'      => ['url' => 'http://erp.wst/t.php?s=/Transport/OfflineCarrierGetTracknum/ibGetPdf/orderid/'],
        'IB中美专线（快速-带电）' => ['url' => 'http://erp.wst/t.php?s=/Transport/OfflineCarrierGetTracknum/ibGetPdf/orderid/'],
        'jumia Soko物流'       => ['url' => 'http://erp.wst/t.php?s=/Transport/jumia/upOrder/ordersn/'],
        'jumia Soko物流-埃及'  => ['url' => 'http://erp.wst/t.php?s=/Transport/jumia/upOrder/ordersn/'],
        'SLS-shopee专用'       => ['url' => 'http://erp.wst/t.php?s=/Transport/SLS/batchOrder/type/print/ordersn/'],
        'SLS-shopee专用(越南)' => ['url' => 'http://erp.wst/t.php?s=/Transport/SLS/batchOrder/type/print/ordersn/'],
        'SLS-shopee （印度尼西亚）' => ['url' => 'http://erp.wst/t.php?s=/Transport/SLS/batchOrder/type/print/ordersn/'],
        'SLS-shopee（马来西亚）'    => ['url' => 'http://erp.wst/t.php?s=/Transport/SLS/batchOrder/type/print/ordersn/'],
        'SLS-shopee（泰国）'   => ['url' => 'http://erp.wst/t.php?s=/Transport/SLS/batchOrder/type/print/ordersn/'],
        'SLS-shopee（新加坡）' => ['url' => 'http://erp.wst/t.php?s=/Transport/SLS/batchOrder/type/print/ordersn/'],
        'SLS-shopee  Standar EKspres印尼' => ['url' => 'http://erp.wst/t.php?s=/Transport/SLS/batchOrder/type/print/ordersn/'],
        'SLS平邮-shopee专用'         => ['url' => 'http://erp.wst/t.php?s=/Transport/SLS/batchOrder/type/print/ordersn/'],
        'SLS平邮-shopee专用（越南）' => ['url' => 'http://erp.wst/t.php?s=/Transport/SLS/batchOrder/type/print/ordersn/'],
        '圆通-shopee' => ['url' => 'http://erp.wst/t.php?s=/Transport/SLS/batchOrder/type/print/ordersn/'],
        'DHL-shopee（泰国）' => ['url' => 'http://erp.wst/t.php?s=/Transport/SLS/batchOrder/type/print/ordersn/'],
        'LWE' => ['url' => 'http://erp.wst/t.php?s=/Transport/LWE/batchOrderAction/type/print/ordersn/'],

        
        '香港挂号-JHD' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
        '香港平邮-JHD' => ['url' => 'http://erp.wst/t.php?s=/Transport/Logistics/batchSubmit/type/p/id/'],
        '香港wish平邮-JHD' => ['url' => 'http://erp.wst/t.php?s=/Transport/jinhuada/getLable/ebay_id/'],
        'GLS物流' => ['url' => 'http://erp.wst/t.php?s=/Transport/gls/getLable/ebay_id/'],

        'SpeedPAK-经济' => ['url' => 'http://erp.wst/t.php?s=/Transport/ebay/getLabel/ebay_id/'],
        'SpeedPAK-标准' => ['url' => 'http://erp.wst/t.php?s=/Transport/ebay/getLabel/ebay_id/'],
        'EPC-Wish' => ['url' => 'http://erp.wst/t.php?s=/Transport/epc/getLable/id/'],
        'EPC-Wish特货' => ['url' => 'http://erp.wst/t.php?s=/Transport/epc/getLable/id/'],
        '台湾小包挂号-GY' => ['url' => 'http://erp.wst/t.php?s=/Transport/Guoyang/batchOrderAction/type/print/ordersn/'],
        '台湾小包平邮-GY' => ['url' => 'http://erp.wst/t.php?s=/Transport/Guoyang/batchOrderAction/type/print/ordersn/'],

        'JOOM-RU简易' => ['url' => 'http://erp.wst/t.php?s=/Transport/Joom/getLable/id/'],
        'JOOM-RU挂号' => ['url' => 'http://erp.wst/t.php?s=/Transport/Joom/getLable/id/'],
            'JOOM-中欧挂号' => ['url' => 'http://erp.wst/t.php?s=/Transport/Joom/getLable/id/'],
            'JOOM-中欧平邮' => ['url' => 'http://erp.wst/t.php?s=/Transport/Joom/getLable/id/'],
        '欧洲专线平邮-WO' => ['url' => 'http://erp.wst/t.php?s=/Transport/WanOu/batchOrderAction/type/print/ordersn/'],
    ),

    'PDF_ORDER_MAP'=>array(),
      
      'PDF_ERROR_CODE'=>array(
            3=>'物流商返回不明异常',
            2=>'物流后台处理',
            1=>'联系物流商处理',
            4=>'订单需要交运'
      ),
      /**
      *测试人员谭 2018-06-23 14:45:46
      *说明: 这个EUB 很神奇 必须要交运 UBI 也是要交运 否则 白单
      */
      'NEED_JIAOYUN'=>array(
        'EUB'           => 'http://erp.wst/t.php?s=/Transport/EubCarrierApi/multiConsignment/bills/',
        'EUB带电'         => 'http://erp.wst/t.php?s=/Transport/EubCarrierApi/multiConsignment/bills/',
        'UBI澳大利亚专线'     => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiYubao/bills/',
        'UBI墨西哥专线'      => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiYubao/bills/',
        'UBI加拿大专线'      => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiYubao/bills/',
        'UBI澳大利亚平邮（带电）' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiYubao/bills/',
        'UBI澳大利亚平邮'     => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiYubao/bills/',
        'UBI俄罗斯专线'      => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiYubao/bills/',
        'UBI俄罗斯专线（带电）'  => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiYubao/bills/',
        'UBI加拿大专线（带电）'  => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiYubao/bills/',
        'UBI墨西哥专线（带电）'  => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiYubao/bills/',
        'UBI澳大利亚专线（带电）' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiYubao/bills/',
        'UBI欧盟28国-半程查件' => 'http://erp.wst/t.php?s=/Transport/UBICarrier/multiYubao/bills/',
      )
);