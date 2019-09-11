<?php
if (APP_ENV == 'product') {
    // 正式环境
    return array(
        'PIC_HTTP_SERVER' => 'http://img.wisstone.com:8088',
        'PIC_HTTP_CUSTOM_ATTR_PIC_PATH' => 'http://img.wisstone.com:8088/customAttrImage/',
        'SPIDER_SERVER'=>'http://www.data.com',
        'KD_SERVER'=>'http://www.kd.com',
    );
} else {
    // 开发环境
    return array(
        'PIC_HTTP_SERVER' => 'http://local.picture.com',
        'PIC_HTTP_CUSTOM_ATTR_PIC_PATH' => 'http://local.picture.com/customAttrImage/',
        'SPIDER_SERVER'=>'http://local.data.com',
        'KD_SERVER'=>'http://kd.cn',
    );
}

