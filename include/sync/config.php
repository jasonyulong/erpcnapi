<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name config.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2019/4/15
 * @Time: 16:24
 * @Description 配置文件
 */
return array(
    //需要同步仓库ID
    'NEED_SYNC_STORE_DATA' => array(
      '196'
    ),
    //需要同步数据表，字段
    'NEED_SYNC_TABLE_DATA'   => array(
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
    //redis配置
    'REDIS_CONFIG' => array(
        'host'	    =>	'redishost',
        'pwd'       => '',
        'port'	    =>	'6379',
        'db_index'	=>	'4',
        'key'       => 'need_sync_data:',
    ),
);