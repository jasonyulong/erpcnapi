<?php
return array(
    'C_WH_ID'      => 'WH196',
    'CLI_KEY'      => 'D9510A26FA3057D23B1577897F7EB277',
    'TOKEN'        => '0AF0FAE0519E5B25456A27EC3C6FFA1E',
    
    //定义此系统里面的仓库 
    'W_196'        => 196,      #本地仓
    'W_999'        => 999,      #本地虚拟仓
    'W_ID'         => array(196, 999),        #支持仓库ID, 999为预留
    'CURRENT_STORE_ID' => 196,
    'STORE_NAMES'=>[
        '196'=>'金科观澜仓',
        '234'=>'樟坑仓',
    ],
    //合仓ID
    'MERGE_STORE_ID'   =>'234',
    //需要同步的数据表r
    'NEED_SYNC_TABLE_ARR'   => array(
        'ebay_goods'                    => 'updateEbayGoods',
        'ebay_productscombine'          => 'updateEbayProductsCombine',
        'ebay_user'                     => 'updateEbayUser',
        'ebay_onhandle_196'             => 'updateEbayOnHandle196',
        'ebay_onhandle_234'             => 'updateEbayOnHandle234',
        'goods_location_history'        => 'updateLocation',
        'ebay_carrier'                  => 'updateEbayCarrier',
        'goods_location_picker_region'  => 'updatePickerRegion',
        'ebay_carrier_company'          => 'updateEbayCarrierCompany',
        'internal_store_sku'            => 'updateInternalStoreSkuData',
        'ebay_countries'                => 'updateEbayCountriesData',
        'ebay_countries_alias'          => 'updateEbayCountriesAliasData',
        'ebay_currency'                 => 'updateEbayCurrencyData',
        'ebay_glock'                    => 'updateEbayGlockData',
    ),
    //erp redis连接配置
    'ERP_REDIS_CONFIG'  => array(
        'host'          => 'binlogredis',
        'port'          => 6379,
    ),
    //最大执行队列数
    'MAX_QUEUE_LIMIT'   => 3000,
    //redis 分库id
    'REDIS_DATABASE_ID' => 4,
    //接收邮箱地址
    'RECEIVE_EMAIL_ARR' => array(
        '904130199@qq.com',
        'tan@oobest.com',
    ),
    //需要检查同步数据表，字段（与同步数据表字段一致）
    'NEED_CHECK_TABLE_DATA'   => array(
        'ebay_goods'                    => 'goods_id,goods_name,goods_sn,goods_price,goods_count,goods_unit,goods_location,
        goods_weight,goods_note,goods_describe,goods_pic,goods_length,goods_width,goods_height,ebay_user,color,size,
        ebay_packingmaterial,ispacking,goods_ywsbmc,goods_cost,goods_category,labletype',
        'ebay_productscombine'          => 'id,goods_sn,goods_sncombine,notes,ebay_user,add_time,combinestr,istock',
        'ebay_user'                     => 'id,username,password,truename,power,user',
        'ebay_onhandle_196'             => 'id,goods_id,goods_sn,average_cost,goods_name,goods_count,goods_sku,g_location
        ,ebay_user,packingmaterial',
        'ebay_onhandle_234'             => 'id,goods_id,goods_sn,average_cost,goods_name,goods_count,goods_sku,g_location
        ,ebay_user,packingmaterial',
        'goods_location_history'        => 'id,storeid,sku,new_location,origin_location,adduser,create_at,up_location_count',
        'ebay_carrier'                  => 'id,name,ebay_user,status,mark_code,is_online,weightmin,weightmax,sorting_code',
        'goods_location_picker_region'  => 'id,picker_id,picker_name,region,region_start,region_end',
        'ebay_carrier_company'          => 'id,fre_type,sup_name,sup_code,sup_abbr,status',
        'internal_store_sku'            => 'id,sku,storeid,add_user,add_time,allow_location',
        'ebay_countries'                => 'id,char_code,name,create_at,desc,ismain',
        'ebay_countries_alias'          => 'id,pid,name,alias,create_at,memo',
        'ebay_currency'                 => 'id,currency,rates,user,rates_1,base_rates,rates_proportion,rates1_proportion,api_date,save_day,country',
        'ebay_glock'                    => 'id,sku,storeid,count,qty,local,lock,actionTime,status',
    ),
);