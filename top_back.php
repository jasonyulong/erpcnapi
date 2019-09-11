<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>ERP</title>
    <link rel="stylesheet" type="text/css" href="cache/themes/Sugar5/css/style.css" />
    <link rel="stylesheet" type="text/css" href="themes/Sugar5/css/navs.css" />
</head>
<body>
<div id="header" class="no-print">
    <div id="companyLogo"></div>
    <?php
    $sql="select count(id) as count from ebay_inner_msg where reguser like '%,".$_SESSION['id'].",%' and readid not like '%,".$_SESSION['id'].",%'";
    $sql=$dbcon->execute($sql);
    $sql=$dbcon->getResultArray($sql);

    ?>
    <div id="welcome" >
        Welcome, <strong><?php echo $_SESSION['truename']; ?></strong>
        <?php
        if($sql[0]['count']>0){ // 系统消息
            ?>
            <a href="msgmain.php" target="_blank" title="您有系统内部通知未读!"><img style="clear:both;border:none;" src="themes/Sugar5/images/msg.gif"/>(<?php echo $sql[0]['count'];?>)</a>
            <?php
        }else{
            ?>
            <a href="msgmain.php" target="_blank" title="您暂时没有未读的通知"><img style="clear:both;border:none;" src="themes/Sugar5/images/msg.jpg"/>(0)</a>
            <?php
        }
        ?>
        [ <a href='handle.php?action=Logout' class='utilsLink'>Log Out</a> ]
        [<a href='systemuserpass.php?&action=密码修改' class='utilsLink'>修改密码</a> ]
    </div>
    <h1 style="color:#911;padding: 20px;font-size: 20px;margin:0">验货专用ERP</h1>

    <div class="clear"></div>
    <span id='sm_holder'></span>
    <div class="clear"></div>
    <div id="moduleList">
        <ul>
            <li class="noBorder">&nbsp;</li>
            <?php
            $module = $_REQUEST['module'];
            $action = $_REQUEST['action'];
            $user = $_SESSION['user'];
            if($module=='goods'){
                $auditted = $_REQUEST['auditted']?$_REQUEST['auditted']:0;
            }else{
                $auditted=-1;
            }
            /* 检查用户权限*/
            $cpower = explode(",", $_SESSION['power']); //D($cpower);
            ?>


            <?php if (in_array("orders", $cpower)) { ?>
                <li class="haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'orders') echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                        <a href="orderindex.php?module=orders&ostatus=1&action=待处理订单" id="moduleTab_Home">订单管理</a>
                    </span>
                    <span class="notCurrentTabRight">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <b class="b_title">拣货流程</b>
                            <a href="t.php?s=/Package/OrderGroup" target="_blank">创建拣货单</a>
                            <a href="t.php?s=/Package/PrintPickingList/orderPackages/isPrint/0" target="_blank">等待打印</a>
                            <a href="t.php?s=/Package/PrintPickingList/orderPackages/isPrint/1" target="_blank">已打印待确认</a>
                            <a href="t.php?s=/Package/PrintPickingList/orderPackages/isPrint/2" target="_blank">已确认待包装</a>
                            <a href="t.php?s=/Package/PrintPickingList/orderPackages/isPrint/3" target="_blank">已包装完毕</a>
                            <a href="t.php?s=/Package/PickCheck/CheckOrders" target="_blank">异常查看</a>
                        </span>
                        <span class="childmod">
                            <b class="b_title">二次分拣</b>
                            <a href="t.php?s=/Package/SecondPick/" target="_blank">二次分拣</a>
                        </span>

                        <span class="childmod">
                            <b class="b_title">包装流程</b>
                            <a href="t.php?s=/Package/MakeBale/" target="_blank">包装流程</a>
                            <a href="t.php?s=/Package/UserPackageFee/showStatistic.html" target="_blank">包装费用统计</a>
                            <a href="t.php?s=/Package/UserPackageFee/pickerStatistic.html" target="_blank">拣货统计</a>
                            <a href="t.php?s=/Package/PickFee/ZreoFeeSKU.html" target="_blank">包装费用为0</a>
                        </span>
                        <span class="childmod">
                            <b class="b_title">基本配置</b>
                            <?php if (in_array('view_pick_carrier_group', $cpower)) {?>
                                <a href="/t.php?s=/Package/CarrierGroup/groupList.html" target="_blank">运输方式分组配置</a>
                                <a href="/t.php?s=/Package/PickFee/index.html" target="_blank">包装费用</a>
                            <?php }?>
                        </span>
                    </div>
                </li>
            <?php } ?>

            <?php if (in_array("customerservice", $cpower)) { ?>
                <li class="customerService haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if (in_array($module,array('message','rma_index','dispute','faxin','customer','paypal','feedback'))) echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                        <a href="messagecategory.php?module=message&action=Message分类">客服管理</a>
                    </span>
                    <span class="notCurrentTabRight">&nbsp;</span>
                </li>
            <?php } ?>


            <?php if (in_array("inseide_question", $cpower)) { ?>
                <li class="haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'inques') echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                        <a href="inside_ques.php?module=inques&action=内部问题&st=0">内部沟通</a>
                    </span>
                    <span class="notCurrentTabRight">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <!--<b class="b_title">内部问题</b>-->
                            <a href="inside_ques.php?module=inques&action=问题列表&st=0" <?php if($action=='问题列表'){echo 'class="h_action"';}?>>问题列表</a>
                            <a href="inside_quesAdd.php?module=inques&action=添加问题" <?php if($action=='添加问题'){echo 'class="h_action"';}?>>添加问题</a>
                        </span>
                    </div>
                </li>
            <?php }?>
            <?php if (in_array("kandeng", $cpower)) { ?>
                <li class="haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'list') echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                        <a href="listing.php?module=list&action=在线物品&status=0" id="moduleTab_Contacts">刊登管理</a>
                    </span>
                    <span class="notCurrentTabRight">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <b class="b_title">刊登管理</b>
                            <a href="listing.php?module=list&action=在线物品&status=0" <?php if($action=='在线物品'){echo 'class="h_action"';}?>>在线物品</a>
                            <a href="listing.php?module=list&action=结束物品&status=1" <?php if($action=='结束物品'){echo 'class="h_action"';}?>>结束物品</a>
                            <a href="listingimportproducts.php?module=list&action=导入在线物品&status=1" <?php if($action=='导入在线物品'){echo 'class="h_action"';}?>>导入在线物品</a>
                            <a href="listlog.php?module=list&action=操作日志&status=1" <?php if($action=='操作日志'){echo 'class="h_action"';}?>>操作日志</a>
                            <a href="ebayenditem.php?module=list&action=自动下架" <?php if($action=='自动下架'){echo 'class="h_action"';}?>>自动下架</a>
                            <a href="include/listunsoldlogs.php?module=list&action=系统自动下架&status=1" <?php if($action=='下架日志'){echo 'class="h_action"';}?>>下架日志</a>
                            <a href="setStocklist.php?module=list&action=在线调零&status=1" <?php if($action=='在线调零'){echo 'class="h_action"';}?>>在线调零</a>
                        </span>
                    </div>
                </li>
            <?php } ?>

            <?php if(in_array('task',$cpower)){?>
                <li class="haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'task') echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                    <a href="tasklist.php?module=task&action=任务列表" id="moduleTab_Contacts">任务管理</a>
                </span>
                    <span class="notCurrentTabRight">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <b class="b_title">任务操作</b>
                           <a class="<?php if($action=='任务列表'){echo 'h_action';}?>" href="tasklist.php?module=task&action=任务列表">任务列表</a>
                            <?php
                            if(in_array('taskcontrol',$cpower)){ ?>
                                <a class="<?php if($action=='任务调控'){echo 'h_action';}?>" href="taskcontrol.php?module=task&action=任务调控">任务调控</a>
                            <?php } ?>

                            <?php //审核未通过   原来为  分配执行
                            if(in_array('taskset',$cpower)){ ?>
                                <a  class="<?php if($action=='任务配置'){echo 'h_action';}?>" href="taskset.php?module=task&action=任务配置">任务配置</a>
                            <?php }?>
                        </span>
                        <span class="childmod">
                        <b class="b_title">海外仓任务操作</b>
                           <a class="<?php if($action=='海外仓任务列表'){echo 'h_action';}?>" href="out_tasklist.php?module=task&action=海外仓任务列表">海外仓任务列表</a>
                            <?php
                            if(in_array('taskcontrol',$cpower)){ ?>
                                <a class="<?php if($action=='海外仓任务调控'){echo 'h_action';}?>" href="out_taskcontrol.php?module=task&action=海外仓任务调控">海外仓任务调控</a>
                            <?php } ?>
                        </span>
                    </div>
                </li>
            <?php } ?>


            <?php if (in_array("goods_0", $cpower)) { ?>
                <li class="haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'goods') echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                    <a href="addgoodsindex.php?module=goods&auditted=0&action=新建产品" id="moduleTab_Contacts">新品开发</a>
                </span>
                    <span class="notCurrentTabRight">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <b class="b_title">新品开发</b>
                            <?php if(in_array('goods_wish',$cpower)){ ?>
                                <a class="<?php if($auditted==10) echo 'h_action';?>" href="kaifa_wish.php?module=goods&auditted=10&action=wish新品">wish新品</a>
                            <?php } ?>
                            <?php if(in_array('goods_5',$cpower)){ ?>
                                <a class="<?php if($auditted==5) echo 'h_action';?>" href="kaifa_cy.php?module=goods&auditted=5&action=新创意">新创意</a>
                            <?php } ?>
                            <?php if(in_array('goods_7',$cpower)){ ?>
                                <a class="<?php if($auditted==7) echo 'h_action';?>" href="kaifa_xq.php?module=goods&auditted=7&action=初次审核">初次审核</a>
                            <?php }?>
                            <?php if(in_array('goods_0',$cpower)){ ?>
                                <a class="<?php if($auditted==0) echo 'h_action';?>" href="addgoodsindex.php?module=goods&auditted=0&action=开发中">开发中</a>
                            <?php }?>

                            <?php if(in_array('goods_8',$cpower)){ ?>
                                <a class="<?php if($auditted==8) echo 'h_action';?>" href="addgoodsindex.php?module=goods&auditted=8&action=图片处理">图片处理</a>
                            <?php }?>

                            <?php
                            if(in_array('goods_2',$cpower)){ ?>
                                <a class="<?php if($auditted==2) echo 'h_action';?>" href="addgoodsindex.php?module=goods&auditted=2&action=待审批货品">审批</a>
                            <?php } ?>

                            <?php //审核未通过   原来为  分配执行
                            if(in_array('goods_3',$cpower)){ ?>
                                <a  class="<?php if($auditted==3) echo 'h_action';?>" href="addgoodsindex.php?module=goods&auditted=3&action=未通过">未通过</a>
                            <?php }?>

                            <?php //回收站   原来为  采样询价
                            if(in_array('goods_4',$cpower)){ ?>
                                <a class="<?php if($auditted==4) echo 'h_action';?>" href="addgoodsindex.php?module=goods&auditted=4&action=回收站">回收站</a>
                            <?php }?>
                        </span>
                    </div>
                </li>
            <?php } ?>

            <?php if(in_array("goodskdys",$cpower)){?>
                <li class="haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'goodskd') echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                    <a href="addgoodsindex.php?module=goods&auditted=0&action=刊登要素">刊登要素</a>
                </span>
                    <span class="notCurrentTabRight">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                            <b class="b_title">刊登要素</b>
                            <a class="" href="productkdys.php?module=goodskd&action=法语刊登要素&tb=fr">法语刊登要素</a>
                            <a class="" href="productkdys.php?module=goodskd&action=德语刊登要素&tb=de">德语刊登要素</a>
                            <a class="" href="productkdys.php?module=goodskd&action=俄语刊登要素&tb=ru">俄语刊登要素</a>
                            <a class="" href="productkdys.php?module=goodskd&action=意大利语刊登要素&tb=it">意大利语刊登要素</a>
                            <a class="" href="productkdys.php?module=goodskd&action=西班牙语刊登要素&tb=es">西班牙语刊登要素</a>
                        </span>
                    </div>
                </li>
            <?php }?>

            <?php if (in_array("purchase", $cpower)) { ?>
                <li class="haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'purchase')echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                        <a href="purchase_outstockorder.php?module=purchase&action=缺货采购" id="moduleTab_Contacts">采购管理</a></span><span class="notCurrentTabRight">&nbsp;
                    </span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <b class="b_title">采购流程</b>
                            <?php if(in_array("purchase_outstockorder",$cpower)){?>
                                <a href="purchase_stock.php?module=purchase&action=备货采购" <?php if($action=='备货采购'){echo 'class="h_action"';}?>>备货采购</a>
                                <a href="purchase_outstockorder.php?module=purchase&action=缺货采购" <?php if($action=='缺货采购'){echo 'class="h_action"';}?>>缺货采购</a>
                            <?php } ?>
                            <?php if(in_array("purchase_newplan",$cpower)){?>
                                <a href="purchase_newplan.php?module=purchase&action=新建采购计划" <?php if($action=='新建采购计划'){echo 'class="h_action"';}?>>新建采购计划</a>
                            <?php } ?>
                            <?php if(in_array("purchase_newplanorders",$cpower)){?>
                                <a href="purchase_newplanorders.php?module=purchase&action=采购计划单" <?php if($action=='采购计划单'){echo 'class="h_action"';}?>>采购计划单</a>
                            <?php } ?>
                            <?php if(in_array("purchase_productstockalarm",$cpower)){?>
                                <a href="productstockalarm.php?module=purchase&action=智能预警" <?php if($action=='智能预警'){echo 'class="h_action"';}?>>智能预警</a>
                            <?php } ?>
                            <?php if(in_array("purchase_instocking",$cpower)){?>
                                <a href="purchase_instocking.php?module=purchase&action=预定中订单" <?php if($action=='预定中订单'){echo 'class="h_action"';}?>>预定中订单</a>
                            <?php } ?>
                            <?php if(in_array("system_factor",$cpower)||in_array("partner_view",$cpower)){?>
                                <a href="partner.php?module=purchase&action=供应商" <?php if($action=='供应商'){echo 'class="h_action"';}?>>供应商</a>
                            <?php } ?>
                        </span>

                        <span class="childmod">
                        <b class="b_title">入库流程</b>
                            <?php if(in_array("purchase_instock",$cpower)){?>
                                <a href="purchase_instock.php?module=purchase&action=入库单列表" <?php if($action=='入库单列表'){echo 'class="h_action"';}?>>入库单列表</a>
                            <?php } ?>

                            <?php if(in_array("purchase_FinanceChecklist",$cpower)||in_array("purchase_FinanceCheck",$cpower)){?>
                                <a href="purchase_FinanceCheck.php?module=purchase&action=财务审核" <?php if($action=='财务审核'){echo 'class="h_action"';}?>>财务审核</a>
                            <?php } ?>

                            <?php if(in_array("purchase_qcorders",$cpower)){?>
                                <a href="purchase_qcorders.php?module=purchase&action=质检审核" <?php if($action=='质检审核'){echo 'class="h_action"';}?>>质检审核</a>
                            <?php } ?>

                            <?php if(in_array("purchase_qcordersyc",$cpower)){?>
                                <a href="purchase_qcordersyc.php?module=purchase&action=异常入库单" <?php if($action=='异常入库单'){echo 'class="h_action"';}?>>异常入库单</a>
                            <?php } ?>

                            <?php if(in_array("purchase_completeorders",$cpower)){?>
                                <a href="purchase_completeorders.php?module=purchase&action=正常已完成" <?php if($action=='正常已完成'){echo 'class="h_action"';}?>>正常已完成</a>
                            <?php } ?>

                            <?php if(in_array("purchase_completeorders",$cpower)){?>
                                <a href="purchase_qcoverorders.php?module=purchase&action=有异常完成" <?php if($action=='有异常完成'){echo 'class="h_action"';}?>>有异常完成</a>
                            <?php } ?>
                            <?php if(in_array("modaverage",$cpower)){?>
                                <a href="modaveragecost.php?module=purchase&action=修改平均成本" <?php if($action=='修改平均成本'){echo 'class="h_action"';}?>>修改平均成本</a>
                            <?php } ?>
                        </span>
                    </div>
                </li>
            <?php } ?>





            <?php if (in_array("warehouse", $cpower)) { ?>
                <li class="haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'warehouse')echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                        <a href="productindex.php?module=warehouse&action=货品资料管理" id="moduleTab_Contacts">库存管理</a>
                    </span>
                    <span class="notCurrentTabRight">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <b class="b_title">货品资料</b>
                            <?php if(in_array("w_goodsmanage",$cpower)){?>
                                <a href="productindex.php?module=warehouse&action=货品资料管理" <?php if($action=='货品资料管理'){echo 'class="h_action"';}?>>货品资料管理</a>
                              <?php if(in_array("s_gm_read",$cpower)){?>
                                    <a href="platformsalers.php?module=warehouse&action=平台销售人员" <?php if($action=='平台销售人员'){echo 'class="h_action"';}?>>平台销售人员</a>
                                <?php }?>
                            <?php }?>
                            <?php if(in_array("viewcatekfuser",$cpower)){?>
                                <a href="categoryusers.php?module=warehouse&action=类别开发人员" <?php if($action=='类别开发人员'){echo 'class="h_action"';}?>>类别开发人员</a>
                            <?php }?>
                            <?php if(in_array("w_goodstype",$cpower)){?>
                                <a href="productcategory.php?module=warehouse&action=货品类别管理" <?php if($action=='货品类别管理'){echo 'class="h_action"';}?>>货品类别管理</a>
                                <a href="suptype.php?module=warehouse&action=货源类型管理" <?php if($action=='货源类型管理'){echo 'class="h_action"';}?>>货源类型管理</a>
                            <?php }?>
                            <?php if(in_array("w_goodscombine",$cpower)){?>
                                <a href="productcombine.php?module=warehouse&action=组合产品管理" <?php if($action=='组合产品管理'){echo 'class="h_action"';}?>>组合产品管理</a>
                            <?php }?>
                            <?php if(in_array("w_material",$cpower)){?>
                                <a href="packingmaterial.php?module=warehouse&action=包装材料管理" <?php if($action=='包装材料管理'){echo 'class="h_action"';}?>>包装材料管理</a>
                            <?php }?>
                            <a href="productscz.php?module=warehouse&action=产品材质管理" <?php if($action=='产品材质管理'){echo 'class="h_action"';}?>>产品材质管理</a>
                            <a href="kdskudecode.php?module=warehouse&action=SKU在线转换" <?php if($action=='SKU在线转换'){echo 'class="h_action"';}?>>SKU在线转换</a>
                            <a href="dccategory.php?module=warehouse&action=DC分类" <?php if($action=='DC分类'){echo 'class="h_action"';}?>>DC分类</a>
                            <?php if(in_array("w_goodsattr",$cpower)){?>
                                <a href="skuattribute.php?module=warehouse&action=SKU状态属性" <?php if($action=='SKU状态属性'){echo 'class="h_action"';}?>>SKU状态属性</a>
                            <?php } ?>
                        </span>

                        <span class="childmod">
                        <b class="b_title">仓库相关</b>
                            <?php if(in_array("w_stockmanage",$cpower)){?>
                                <a href="productwarehouse.php?module=warehouse&action=仓库管理" <?php if($action=='仓库管理'){echo 'class="h_action"';}?>>仓库管理</a>
                            <?php }?>
                            <?php if(in_array("w_iostore",$cpower)){?>
                                <a href="productwarehousetype.php?module=warehouse&action=出入库类型" <?php if($action=='出入库类型'){echo 'class="h_action"';}?>>出入库类型</a>
                            <?php }?>
                            <?php if(in_array("w_instore",$cpower)){?>
                                <a href="instore.php?module=warehouse&action=入库单&stype=0&dstatus=0" <?php if($action=='入库单'){echo 'class="h_action"';}?>>入库单</a>
                            <?php }?>
                            <?php if(in_array("w_outstore",$cpower)){?>
                                <a href="outstore.php?module=warehouse&action=出库单&stype=1&dstatus=0" <?php if($action=='出库单'){echo 'class="h_action"';}?>>出库单</a>
                            <?php }?>
                            <?php if(in_array("w_allocate",$cpower)){?>
                                <a href="wtowarehouse.php?module=warehouse&action=调拨单&stype=3&dstatus=0" <?php if($action=='调拨单'){echo 'class="h_action"';}?>>调拨单</a>
                            <?php }?>
                            <a href="wtosamorder.php?module=warehouse&action=组装单&stype=4&dstatus=6" <?php if($action=='组装单'){echo 'class="h_action"';}?>>组装单</a>
                            <a href="wtosambomorder.php?module=warehouse&action=BOM单&stype=4&dstatus=0" <?php if($action=='BOM单'){echo 'class="h_action"';}?>>BOM单</a>
                            <a href="lockedstore.php?module=warehouse&action=库存锁定" <?php if($action=='库存锁定'){echo 'class="h_action"';}?>>库存锁定</a>

                        </span>

                        <span class="childmod">
                            <?php   if(true||in_array("w_tongji",$cpower)){   ?>
                                <b class="b_title">计算统计</b>

                                <a href="warehousingcheck.php?module=warehouse&action=入库核对" <?php if($action=='入库核对'){echo 'class="h_action"';}?>>入库核对</a>
                                <a href="productalert1.php?module=warehouse&action=库存预警" <?php if($action=='库存预警'){echo 'class="h_action"';}?>>库存预警</a>

                            <?php if(in_array("w_mbaobiao01",$cpower)){?>
                                    <a href="productreport01.php?module=warehouse&action=进销存汇总表" <?php if($action=='进销存汇总表'){echo 'class="h_action"';}?>>进销存汇总表</a>
                                <?php }?>
                            <?php if(in_array("w_pandian",$cpower)){?>
                                    <a href="pandianadd.php?module=warehouse&action=库存盘点&ostatus=0" <?php if($action=='库存盘点'){echo 'class="h_action"';}?>>库存盘点</a>
                                    <a href="pandianadd.php?module=warehouse&status=2" <?php if($action=='盘点审核'){echo 'class="h_action"';}?>>盘点审核</a>
                                    <?php // <a href="pandianindex.php?module=warehouse&action=盘点审核&ostatus=0" <?php if($action=='盘点审核'){echo 'class="h_action"';}>盘点审核</a> ?>
                                <?php } ?>
                            <?php 	if(in_array("pcost",$cpower)){	 ?>
                                    <a href="pcalc.php?module=warehouse&action=销售价计算&ostatus=0" <?php if($action=='销售价计算'){echo 'class="h_action"';}?>>销售价计算</a>
                                <?php } ?>
                            <a href="pcalc2.php?module=warehouse&action=调价报表&ostatus=0" <?php if($action=='调价报表'){echo 'class="h_action"';}?>>调价报表</a>
                                <a href="pcalc3.php?module=warehouse&action=缺货报表&ostatus=0" <?php if($action=='缺货报表'){echo 'class="h_action"';}?>>缺货报表</a>
                            <?php if(in_array("valuefloat",$cpower)){?>
                                    <a href="countgoodsvalue.php?module=warehouse&action=库存价值浮动&ostatus=0" <?php if($action=='库存价值浮动'){echo 'class="h_action"';}?>>库存价值浮动</a>
                                <?php  } ?>
                            <?php  } ?>

                        </span>
                        <span class="childmod">
						    <?php 	if(in_array("goods_out",$cpower)){	 ?>
                                <b class="b_title">淘汰SKU管理</b>
                            <?php  } ?>
                            <?php 	if(in_array("goods_out_1",$cpower)){	 ?>
                                <a href="goods_out.php?module=warehouse&action=淘汰中&status=1" <?php if($action=='淘汰中'){echo 'class="h_action"';}?>>淘汰中</a>
                            <?php } ?>
                            <?php 	if(in_array("goods_out_2",$cpower)){	 ?>
                                <a href="goods_out.php?module=warehouse&action=库存清理&status=2" <?php if($action=='库存清理'){echo 'class="h_action"';}?>>库存清理</a>
                            <?php } ?>
                            <?php 	if(in_array("goods_out_3",$cpower)){	 ?>
                                <a href="goods_out.php?module=warehouse&action=仓库确认&status=3" <?php if($action=='仓库确认'){echo 'class="h_action"';}?>>仓库确认</a>
                            <?php } ?>
                            <?php 	if(in_array("goods_out_4",$cpower)){	 ?>
                                <a href="goods_out.php?module=warehouse&action=确认淘汰&status=4" <?php if($action=='确认淘汰'){echo 'class="h_action"';}?>>确认淘汰</a>
                            <?php } ?>
						</span>
                        <span class="childmod">
						    <?php 	if(in_array("returnsku",$cpower)){	 ?>
                                <b class="b_title">退换货管理</b>
                            <?php  } ?>
                            <?php 	if(in_array("returnsku0",$cpower)){	 ?>
                                <a href="returnindex.php?module=warehouse&action=编辑草稿&status=0" <?php if($action=='编辑草稿'){echo 'class="h_action"';}?>>编辑草稿</a>
                            <?php } ?>
                            <?php 	if(in_array("returnsku2",$cpower)){	 ?>
                                <a href="returnindex.php?module=warehouse&action=入库进行中&status=2" <?php if($action=='入库进行中'){echo 'class="h_action"';}?>>入库进行中</a>
                            <?php } ?>
                            <?php 	if(in_array("returnsku3",$cpower)){	 ?>
                                <a href="returnindex.php?module=warehouse&action=入库完结&status=3" <?php if($action=='入库完结'){echo 'class="h_action"';}?>>入库完结</a>
                            <?php } ?>
                            <?php 	if(in_array("returnsku4",$cpower)){	 ?>
                                <a href="returnindex.php?module=warehouse&action=入库异常&status=4" <?php if($action=='入库异常'){echo 'class="h_action"';}?>>入库异常</a>
                            <?php } ?>
                            <?php if (in_array("returnreason", $cpower)) { ?>
                                <b class="b_title">退换货原因</b>
                <?php 	if(in_array("returnreasonlist",$cpower)){	 ?>
                                    <a href="returnreason.php?module=warehouse&action=退换货原因" <?php if($action=='退换货原因'){echo ' class="h_action"';}?>>退换货原因</a>
                                <?php } ?>
                            <?php }?>
                            <?php if (in_array("overseasaudit", $cpower)) { ?>
                                <b class="b_title">海外仓SKU审核</b>
                <?php 	if(in_array("overseasaudit",$cpower)){	 ?>
                                    <a href="overseasgoodsaudit.php?module=overseasaudit&action=审核前&status=1" <?php if($action=='审核前'){echo ' class="h_action"';}?>>海外仓SKU审核</a>
                                <?php } ?>
                            <?php }?>
						</span>
                    </div>

                </li>
            <?php } ?>


            <?php if (in_array("inbound", $cpower)) { ?>
                <li class="haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'package')echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                     <a href="package_newplan.php?module=package&action=发货计划" id="moduleTab_Contacts">海外仓管理</a></span><span class="notCurrentTabRight">&nbsp;
</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                     <b class="b_title">海外仓管理</b>
                            <?php if(in_array("package_newplan",$cpower)){ ?>
                                <a href="package_newplan.php?module=package&action=发货计划" <?php if($action=='发货计划'){echo 'class="h_action"';}?>>发货计划</a>
                                <a href="package_newplan_whs.php?module=package&action=等待配货" <?php if($action=='等待配货'){echo 'class="h_action"';}?>>等待配货</a>
                                <a href="package_newplan_wpk.php?module=package&action=等待装箱" <?php if($action=='等待装箱'){echo 'class="h_action"';}?>>等待装箱</a>
                            <?php    } ?>
                            <?php if(in_array("packagelist_drafe",$cpower)){ ?>
                                <a href="packagelist_drafe.php?module=package&action=等待审核" <?php if($action=='等待审核'){echo 'class="h_action"';}?>>等待审核</a>
                            <?php }?>
                            <?php if(in_array("packagelist_submit",$cpower)){ ?>
                                <a href="packagelist_submit.php?module=package&action=等待扫描" <?php if($action=='等待扫描'){echo 'class="h_action"';}?>>等待扫描</a>
                            <?php }?>
                            <?php if(in_array("packagelist_transit",$cpower)){ ?>
                                <a href="packagelist_transit.php?module=package&action=在途订单" <?php if($action=='在途订单'){echo 'class="h_action"';}?>>在途订单</a>
                            <?php }?>
                            <?php if(in_array("packagelist_shelved",$cpower)){ ?>
                                <a href="packagelist_shelved.php?module=package&action=已上架订单" <?php if($action=='已上架订单'){echo 'class="h_action"';}?>>已上架订单</a>
                            <?php }?>
                            <?php if(in_array("packagelist_void",$cpower)){ ?>
                                <a href="packagelist_void.php?module=package&action=作废订单" <?php if($action=='作废订单'){echo 'class="h_action"';}?>>作废订单</a>
                            <?php }?>

</span>
                    </div>
                </li>
            <?php } ?>

            <?php if (in_array("super_customer", $cpower)) { //大客户	 ?>
                <li class="notCurrentTabLeft haveChild">
                    <span>&nbsp;</span>
                    <span class="<?php if ($module == 'supercustemer' || $module == 'super_customer')echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                        <a href="super_customers.php?module=super_customer&action=大客户信息" id="moduleTab_Contacts">大客户管理</a>
                    </span>
                    <span class="notCurrentTabRight">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <b class="b_title">大客户管理</b>
                            <a href="super_customers.php?module=super_customer&action=大客户信息" class="<?php if($_REQUEST['action']=='大客户信息'){echo 'h_action';};?>">大客户信息</a>
                            <a href="super_customers_orders.php?module=super_customer&action=大客户订单" class="<?php if($_REQUEST['action']=='大客户订单'){echo 'h_action';};?>">大客户订单</a>
                            <a href="super_customers_level.php?module=super_customer&action=客户类型" class="<?php if($_REQUEST['action']=='客户类型'){echo 'h_action';};?>">客户类型</a>
                        </span>
                    </div>
                </li>
            <?php } ?>



            <?php if (in_array("sales", $cpower)) { ?>
                <li class="notCurrentTabLeft haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'finance')echo 'currentTab'; else echo 'notCurrentTab';?>">
                        <a href="paypaltindex.php?module=finance&action=Paypal销售额统计" id="moduleTab_Opportunities">销售分析</a>
                    </span>
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <b class="b_title">ebay分析</b>
                            <?php if(in_array("ebay_sale_statistics",$cpower)){ ?>
                                <a href="ordertongji2.php?module=finance&action=销售额统计&type=1" <?php if($action=='销售额统计'){echo 'class="h_action"';}?>>销售额统计</a>
                            <?php } ?>
                            <?php if(in_array("paypal_sale_statistics",$cpower)){ ?>
                                <a href="paypaltindex.php?module=finance&action=Paypal销售额统计" <?php if($action=='Paypal销售额统计'){echo 'class="h_action"';}?>>Paypal销售额统计</a>
                            <?php } ?>
                            <?php if(in_array("paypal_table_view",$cpower)){ ?>
                                <a href="paypaldetail.php?module=finance&action=Paypal明细查看" <?php if($action=='Paypal明细查看'){echo 'class="h_action"';}?>>Paypal明细查看</a>
                            <?php } ?>
                            <?php if(in_array("ebay_table_view",$cpower)){ ?>
                                <a href="ebaydetail.php?module=finance&action=eBay明细查看" <?php if($action=='eBay明细查看'){echo 'class="h_action"';}?>>eBay明细查看</a>
                            <?php } ?>
                            <?php if(in_array("synchronous_paypal_sale",$cpower)){ ?>
                                <a href="paypalimport.php?module=finance&action=同步Paypal销售额" <?php if($action=='同步Paypal销售额'){echo 'class="h_action"';}?>>同步Paypal销售额</a>
                            <?php } ?>
                            <?php if(in_array("synchronous_ebay_fee",$cpower)){ ?>
                                <a href="ebayimport.php?module=finance&action=同步ebay费用" <?php if($action=='同步ebay费用'){echo 'class="h_action"';}?>>同步ebay费用</a>
                            <?php } ?>
                            <?php if(in_array("orders_profit_unrecognize",$cpower)){ ?>
                                <a href="orderindexlr.php?module=finance&action=订单利润未确认&ostatus=100&profitstatus=0" <?php if($action=='订单利润未确认'){echo 'class="h_action"';}?>>订单利润未确认[新]</a>
                            <?php } ?>
                            <?php if(in_array("orders_profit_recognize",$cpower)){ ?>
                                <a href="orderindexlred.php?module=finance&action=订单利润已确认&ostatus=100&profitstatus=1" <?php if($action=='订单利润已确认'){echo 'class="h_action"';}?>>订单利润已确认[新]</a>
                            <?php } ?>
                        </span>
                        <span class="childmod">
                        <b class="b_title">系统分析</b>
                            <?php if(in_array("product_sale_analyse",$cpower)){ ?>
                                <a href="productprofit.php?module=finance&action=产品销售分析" <?php if($action=='产品销售分析'){echo 'class="h_action"';}?>>产品销售分析</a>
                            <?php } ?>
                            <?php if(in_array("sale_profit_sum_table",$cpower)){ ?>
                                <a href="ordertongji.php?module=finance&action=销售/利润汇总表" <?php if($action=='销售/利润汇总表'){echo 'class="h_action"';}?>>销售/利润汇总表</a>
                            <?php } ?>
                            <?php if(in_array("sale_table",$cpower)){?>
                                <a href="sale_to_table.php?module=finance&table=1&action=销售明细-按付款时间" <?php if($action=='销售明细-按付款时间'){echo 'class="h_action"';}?>>销售明细-按付款时间</a>
                            <?php }?>
                            <?php if(in_array("sale_table_bytime",$cpower)){?>
                                <a href="sale_to_table.php?module=finance&table=2&action=销售明细-按确认时间" <?php if($action=='销售明细-按确认时间'){echo 'class="h_action"';}?>>销售明细-按确认时间</a>
                            <?php }?>
                            <?php if(in_array("ordergain_print1",$cpower)){?>
                                <a href="ordergain_print1.php?module=finance&action=订单利润表" <?php if($action=='订单利润表'){echo 'class="h_action"';}?>>订单利润表</a>
                            <?php } ?>
                            <?php if(in_array("ordergain_print",$cpower)){?>
                                <a href="ordergain_print.php?module=finance&action=订单利润明细表" <?php if($action=='订单利润明细表'){echo 'class="h_action"';}?>>订单利润明细表</a>
                            <?php } ?>
                        </span>

                    </div>
                </li>
            <?php } ?>

            <?php if (in_array("report", $cpower)) { ?>
                <li class="notCurrentTabLeft haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'report')echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                        <a href="control_rate.php?module=report&action=发货达标率报表" id="moduleTab_Opportunities">统计报表</a>
                    </span>
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <b class="b_title">报表数据</b>
                            <?php if(in_array("control_rate",$cpower)){?>
                                <a href="control_rate.php?module=report&action=发货达标率报表" <?php if($action=='发货达标率报表'){echo 'class="h_action"';}?>>发货达标率报表</a>
                            <?php } ?>
                            <a href="task_report.php?module=report&table=2&action=任务完成达标率报表" <?php if($action=='任务完成达标率报表'){echo 'class="h_action"';}?>>任务完成达标率报表</a>
                            <?php if(in_array("cgcheck_rate",$cpower)){?>
                                <a href="cgcheck.php?module=report&action=采购人员绩效考核表" >采购人员绩效考核表</a>
                            <?php } ?>
                            <?php if(in_array("kfcheck_rate",$cpower)){?>
                                <a href="kfcheck.php?module=report&action=开发人员绩效考核表(开发成功)&type=1" >开发人员绩效考核表</a>
                            <?php } ?>
                            <?php if(in_array("salerate",$cpower)){?>
                                <a href="salerate.php?module=report&table=2&action=SKU销频涨跌明细表" <?php if($action=='SKU销频涨跌明细表'){echo 'class="h_action"';}?>>SKU销频涨跌明细表</a>
                            <?php } ?>
                            <?php if(in_array("sale_sku_dms",$cpower)){?>
                                <a href="sale_sku_store_dms.php?module=report&table=2&action=SKU评估" <?php if($action=='SKU评估'){echo 'class="h_action"';}?>>SKU评估(按仓库)</a>
                                <a href="sale_sku_daihuo.php?module=report&table=2&action=呆货分析" <?php if($action=='呆货分析'){echo 'class="h_action"';}?>>呆货分析</a>
                                <a href="sale_sku_zhiku.php?module=report&table=2&action=滞库分析" <?php if($action=='滞库分析'){echo 'class="h_action"';}?>>滞库分析</a>
                            <?php } ?>
                            <?php if(in_array("unsold",$cpower)){?>
                                <a href="unsold.php?module=report&table=2&action=滞销品导出" <?php if($action=='滞销品导出'){echo 'class="h_action"';}?>>滞销SKU导出</a>
                            <?php } ?>
                            <?php if(in_array("sku_trans_ratio2",$cpower)){?>
                                <a href="sku_trans_ratio2.php?module=report&action=SKU转化率" <?php if($action=='SKU转化率'){echo 'class="h_action"';}?>>SKU转化率</a>
                                <a href="list_trans_ratio.php?module=report&action=list转化率" <?php if($action=='list转化率'){echo 'class="h_action"';}?>>list转化率</a>
                                <a href="skustatistics.php?module=report&action=动销率考核" <?php if($action=='动销率考核'){echo 'class="h_action"';}?>>动销率考核</a>
                            <?php } ?>
                            <a href="shipfeeindex.php?module=report&action=运费核对" >运费核对</a>
                       		  <a href="overseaskfskus.php?module=report&action=海外仓可售卖SKU" >海外仓可售卖SKU</a>
                       		  <a href="weightstatistics.php?module=report&action=发货重量区间统计" >发货重量区间统计</a>
                        </span>
                        <span class="childmod">
                        <b class="b_title">其他数据</b>
                            <?php if(in_array("countplatsorder",$cpower))?>
                            <a href="platformcount.php?module=report&action=各平台订单统计" <?php if($action=='各平台订单统计'){echo 'class="h_action"';}?>>各平台订单统计</a>
                            <?php ?>
                        </span>
                    </div>
                </li>
            <?php } ?>


            <!--
            <li class="notCurrentTabLeft">
                <span class="notCurrentTabLeft">&nbsp;</span>
                <span class="<?php if ($module == 'advance') echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                    <a href="advance_index.php?module=advance&action=" id="moduleTab_Opportunities">广告管理</a>
                </span>
                <span class="notCurrentTabLeft">&nbsp;</span>
            </li>
-->
            <?php if (in_array("dataaudit", $cpower)) { ?>
                <li class="notCurrentTabLeft haveChild">
                    <span>&nbsp;</span>
                    <span class="<?php if ($module == 'data_audit')echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                        <a href="dataaudit.php?module=data_audit&action=数据审核" id="moduleTab_Contacts">数据审核</a>
                    </span>
                    <span class="notCurrentTabRight">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <b class="b_title">数据审核</b>
                            <?php if (in_array("dataaudit", $cpower)) { ?><a href="dataaudit.php?module=data_audit&action=待执行&auditstatus=1" class="<?php if($_REQUEST['action']=='待执行'){echo 'h_action';};?>">待执行</a><?php } ?>
                            <?php if (in_array("dataaudit", $cpower)) { ?><a href="dataaudit.php?module=data_audit&action=执行成功&auditstatus=2" class="<?php if($_REQUEST['action']=='执行成功'){echo 'h_action';};?>">执行成功</a><?php } ?>
                            <?php if (in_array("dataaudit", $cpower)) { ?><a href="dataaudit.php?module=data_audit&action=执行失败&auditstatus=3" class="<?php if($_REQUEST['action']=='执行失败'){echo 'h_action';};?>">执行失败</a><?php } ?>
                            <?php if (in_array("dataaudit", $cpower)) { ?><a href="dataaudit.php?module=data_audit&action=拒绝执行&auditstatus=4" class="<?php if($_REQUEST['action']=='拒绝执行'){echo 'h_action';};?>">拒绝执行</a><?php } ?>
                         </span>
                    </div>
                </li>
            <?php } ?>

            <?php if (in_array("system", $cpower)) { ?>
                <li class="haveChild">
                    <span class="notCurrentTabLeft">&nbsp;</span>
                    <span class="<?php if ($module == 'system')echo 'currentTab'; else echo 'notCurrentTab'; ?>">
                        <a href="systemebay.php?module=system&action=eBay帐号管理" id="moduleTab_Leads">系统管理</a>
                    </span><span class="notCurrentTabRight">&nbsp;</span>
                    <div class="openDiv">
                        <span class="lineMask"></span>
                        <span class="childmod">
                        <b class="b_title">账号设置</b>
                            <?php if(in_array("system_ebayaccount",$cpower)){?>
                                <a href="systemebay.php?module=system&action=eBay帐号管理" <?php if($action=='eBay帐号管理'){echo 'class="h_action"';}?>>eBay帐号管理</a>
                            <?php } ?>
                            <?php if(in_array("system_amazonaccount",$cpower)){?>
                                <a href="systeamazonaccount.php?module=system&action=Amazon帐号管理" <?php if($action=='Amazon帐号管理'){echo 'class="h_action"';}?>>Amazon帐号管理</a>
                            <?php } ?>

                            <?php if(0&&in_array("system_zencartaccount",$cpower)){?>
                                <a href="systemzencart.php?module=system&action=ZenCart帐号管理" <?php if($action=='ZenCart帐号管理'){echo 'class="h_action"';}?>>ZenCart帐号管理</a>
                            <?php } ?>

                            <?php if(0&&in_array("system_magentoaccount",$cpower)){?>
                                <a href="systemmagento.php?module=system&action=Magento帐号管理" <?php if($action=='Magento帐号管理'){echo 'class="h_action"';}?>>Magento帐号管理</a>
                            <?php } ?>

                            <?php if(in_array("system_papalaccount",$cpower)){?>
                                <a href="paypalindex.php?module=system&action=Paypal帐号管理" <?php if($action=='Paypal帐号管理'){echo 'class="h_action"';}?>>Paypal帐号管理</a>
                            <?php } ?>
                            <?php if(in_array("system_wishaccount",$cpower)){?>
                                <a href="wishindex.php?module=system&action=WISH帐号管理" <?php if($action=='WISH帐号管理'){echo 'class="h_action"';}?>>WISH帐号管理</a>
                            <?php } ?>
                            <?php if(in_array("system_skuaccount",$cpower)){?>
                                <a href="skuaccount.php?module=system&action=SKU编码设置" <?php if($action=='SKU编码设置'){echo 'class="h_action"';}?>>SKU编码设置</a>
                            <?php } ?>
                            <?php if(in_array("system_modpass",$cpower)){?>
                                <a href="systemuserpass.php?module=system&action=密码修改" <?php if($action=='密码修改'){echo 'class="h_action"';}?>>密码修改</a>
                            <?php } ?>
                            </span>

                        <span class="childmod">
                            <b class="b_title">系统参数设置</b>

                            <?php if(in_array("sysetm_currency",$cpower)){?>
                                <a href="systemrates.php?module=system&action=汇率设置" <?php if($action=='汇率设置'){echo 'class="h_action"';}?>>汇率设置</a>
                            <?php } ?>

                            <?php if(in_array("control_add",$cpower)){?>
                                <a href="control_add.php?module=system&action=达标率标准设置" <?php if($action=='达标率标准设置'){echo 'class="h_action"';}?>>达标率标准设置</a>
                            <?php } ?>

                            <?php if(in_array("system_orderstatus",$cpower)){?>
                                <a href="systemcustommenu.php?module=system&action=订单分类" <?php if($action=='订单分类'){echo 'class="h_action"';}?>>订单分类</a>
                            <?php } ?>

                            <?php if(in_array("system_config",$cpower)){?>
                                <!--<a href="systemordershide.php?module=system&action=订单清理">订单清理</a> -->
                                <a href="systemconfig.php?module=system&action=系统配置" <?php if($action=='系统配置'){echo 'class="h_action"';}?>>系统配置</a>
                            <?php } ?>

                            <?php if(in_array("system_goodsstatus",$cpower)){?>
                                <a href="systemgoodsstatus.php?module=system&action=物品状态" <?php if($action=='物品状态'){echo 'class="h_action"';}?>>物品状态</a>
                            <?php } ?>

                            <?php if(in_array("platform_cost_config",$cpower)){?>
                                <a href="goodspitai.php?module=system&action=平台费用设置" <?php if($action=='平台费用设置'){echo 'class="h_action"';}?>>平台费用设置</a>
                            <?php }?>
                            <?php if(in_array("worktime",$cpower)){?>
                                <a href="worktime.php?module=system&action=工作时间段设置" <?php if($action=='工作时间段设置'){echo 'class="h_action"';}?>>工作时间段设置</a>
                            <?php } ?>
                            <?php if(in_array("system_develop_config",$cpower)){?>
                                <a href="systemkfgoods.php?module=system&action=开发设置" <?php if($action=='开发设置'){echo 'class="h_action"';}?>>开发设置</a>
                            <?php } ?>
                            <?php if(in_array("system_task_config",$cpower)){?>
                                <a href="systemovertask.php?module=system&action=海外仓任务" <?php if($action=='海外仓任务'){echo 'class="h_action"';}?>>海外仓任务</a>
                            <?php } ?>
                            <?php if(in_array("system_return_rule",$cpower)){?>
                                <a href="systemreturnrule.php?module=system&action=自动退款配置" <?php if($action=='自动退款配置'){echo 'class="h_action"';}?>>自动退款配置</a>
                            <?php } ?>
                            <?php if(in_array("storenotemod",$cpower)){?>
                                <a href="storenote.php?module=system&action=仓库发货说明" <?php if($action=='仓库发货说明'){echo 'class="h_action"';}?>>仓库发货说明</a>
                            <?php } ?>
                            </span>

                        <span class="childmod">
                            <b class="b_title">其他杂项</b>
                            <?php if(in_array("sytem_carrier",$cpower)){?>
                                <a href="systemaddress.php?module=system&action=收发地址" <?php if($action=='收发地址'){echo 'class="h_action"';}?>>收发地址</a>
                                <a href="systemcarrier.php?module=system&action=发货方式" <?php if($action=='发货方式'){echo 'class="h_action"';}?>>发货方式</a>
                            <?php } ?>

                            <?php if(in_array("system_region",$cpower)){?>
                                <a href="systemcountrys.php?module=system&action=地区列表" <?php if($action=='地区列表'){echo 'class="h_action"';}?>>地区列表</a>
                            <?php } ?>

                            <?php if(in_array("system_ordertype",$cpower)){?>
                                <a href="systemuordertype.php?module=system&action=订单类型管理" <?php if($action=='订单类型管理'){echo 'class="h_action"';}?>>订单类型管理</a>
                            <?php } ?>

                            <?php if(in_array("system_users",$cpower)){?>
                                <a href="systemusers.php?module=system&action=用户管理" <?php if($action=='用户管理'){echo 'class="h_action"';}?>>用户管理</a>
                            <?php } ?>
                            <?php if(in_array("system_users",$cpower)){?>
                                <a href="systemrole.php?module=system&action=角色管理" <?php if($action=='角色管理'){echo 'class="h_action"';}?>>角色管理</a>
                            <?php } ?>
                            <?php if(in_array("notbeihuo",$cpower)){?>
                                <a href="notbeihuo.php?module=system&action=不备货SKU" <?php if($action=='不备货SKU'){echo 'class="h_action"';}?>>不备货SKU</a>
                            <?php } ?>
                            <?php if(in_array("moneyset",$cpower)){?>
                                <a href="moneyset.php?module=system&action=货币费设置" <?php if($action=='货币费设置'){echo 'class="h_action"';}?>>货币费设置</a>
                            <?php } ?>
                            <?php if(in_array("amzskuset",$cpower)){?>
                                <a href="systemamzskuset.php?module=system&action=亚马逊SKU对应表" <?php if($action=='亚马逊SKU对应表'){echo 'class="h_action"';}?>>亚马逊SKU</a>
                            <?php } ?>
                            <?php if(in_array("systemlog",$cpower)){?>
                                <a href="systemlog.php?module=system&action=操作日志" <?php if($action=='操作日志'){echo 'class="h_action"';}?>>操作日志</a>
                            <?php } ?>
                            <?php /*if(in_array("systemlog",$cpower)){*/?><!--
                                <a href="systemchecklog.php?module=system&action=操作日志" <?php /*if($action=='日志文件'){echo 'class="h_action"';}*/?>>日志文件</a>
                            --><?php /*} */?>
                        </span>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
    <!--end oduleList-->
    <div class="clear"></div>
    <div class="line"></div>

    <div id="shortcuts" class="headerList">
        <span class="child_list_box">

    <!-- 业务管理-->

            <?php //<!-- 业务管理-->
            if($module == 'sale'){ ?>
                <a href="salesindex.php?module=sale&action=利润计算">利润计算</a>
                <a href="add_products_add.php?module=sale&action=产品开发">&nbsp;<span>产品开发</span></a>
                <a href="add_products_price.php?module=sale&action=产品优化">&nbsp;<span>产品优化</span></a>
                <a href="qs_list.php?module=sale&action=FAQ(知识库)">&nbsp;<span>FAQ(知识库)</span></a>

            <?php } ?>

            <?php //<!-- 海外仓审核-->
            if($module == 'overseasaudit'){ ?>
                <a href="overseasgoodsaudit.php?module=overseasaudit&action=审核前&status=1"  class="<?php if($_REQUEST['action']=='审核前'){echo 'a_hover';};?>">审核前</a>
                <a href="overseasgoodsaudit.php?module=overseasaudit&action=审核中&status=2"  class="<?php if($_REQUEST['action']=='审核中'){echo 'a_hover';};?>">&nbsp;<span>审核中</span></a>
                <a href="overseasgoodsaudit.php?module=overseasaudit&action=已通过&status=4"  class="<?php if($_REQUEST['action']=='已通过'){echo 'a_hover';};?>">&nbsp;<span>已通过</span></a>
                <a href="overseasgoodsaudit.php?module=overseasaudit&action=未通过&status=3"  class="<?php if($_REQUEST['action']=='未通过'){echo 'a_hover';};?>">&nbsp;<span>未通过</span></a>

            <?php } ?>

            <?php //<!-- 刊登要素-->
            if($module == 'goodskd'){ ?>
                <a href="productkdys.php?module=goodskd&action=<?php echo $_REQUEST['action'];?>&cp=0&tb=<?php echo $_REQUEST['tb'];?>"  class="<?php if((int)$_REQUEST['cp']==0){echo 'a_hover';};?>">等待编辑</a>
                <a href="productkdys.php?module=goodskd&action=<?php echo $_REQUEST['action'];?>&cp=1&tb=<?php echo $_REQUEST['tb'];?>"  class="<?php if((int)$_REQUEST['cp']==1){echo 'a_hover';};?>">&nbsp;<span>编辑中</span></a>
                <a href="productkdys.php?module=goodskd&action=<?php echo $_REQUEST['action'];?>&cp=2&tb=<?php echo $_REQUEST['tb'];?>"  class="<?php if((int)$_REQUEST['cp']==2){echo 'a_hover';};?>">&nbsp;<span>编辑完成</span></a>
            <?php } ?>


            <?php /*//<!-- 评价管理-->
    if($module == 'feedback'){
    ?>
    <a href="feedbacktj.php?module=feedback&action=评价报表">&nbsp;<span>评价报表</span></a>
    <a href="feedbacksys.php?module=feedback&action=评价加载">&nbsp;<span>评价加载</span></a>
    <a href="feedback.php?module=feedback&action=评价管理">&nbsp;<span>评价管理</span></a>
    <a href="feedbackitbb.php?module=feedback&action=itemnumber报表">&nbsp;<span>itemnumber报表</span></a>
    <?php } */?>


            <?php //Paypal 所有退款
            if($module == 'paypalrefund'){ ?>
                <a href="paypal_refund.php?module=paypalrefund&status=&action=所有退款申请" <?php if($action=='所有退款申请'){echo 'class="a_hover"';}?>>&nbsp;<span>所有退款申请
    (<font color="#FF0000"><?php
                            $sql	= "select count(ebay_id) as cc from ebay_paypalrefund where ebay_user='$user' ";
                            $sql	= $dbcon->execute($sql);
                            $sql	= $dbcon->getResultArray($sql);
                            echo $sql[0]['cc'];
                            ?></font>)
    </span></a>


                <a href="paypal_refund.php?module=paypalrefund&status=0&action=Paypal退款待审核" <?php if($action=='Paypal退款待审核'){echo 'class="a_hover"';}?>>&nbsp;<span>Paypal退款待审核
    (<font color="#FF0000"><?php
                            $sql	= "select count(ebay_id) as cc from ebay_paypalrefund where ebay_user='$user' and status = 0";
                            $sql	= $dbcon->execute($sql);
                            $sql	= $dbcon->getResultArray($sql);
                            echo $sql[0]['cc'];
                            ?></font>)
    </span></a>

                <a href="paypal_refund.php?module=paypalrefund&status=1&action=Paypal退款已审核" <?php if($action=='Paypal退款已审核'){echo 'class="a_hover"';}?>>&nbsp;<span>Paypal退款已审核(<font color="#FF0000"><?php
                            $sql	= "select count(ebay_id) as cc from ebay_paypalrefund where ebay_user='$user' and status = 1";
                            $sql	= $dbcon->execute($sql);
                            $sql	= $dbcon->getResultArray($sql);
                            echo $sql[0]['cc'];
                            ?></font>)
    </span></a>

                <a href="paypal_refund.php?module=paypalrefund&status=2&action=Paypal退款已完成" <?php if($action=='Paypal退款已完成'){echo 'class="a_hover"';}?>>&nbsp;<span>Paypal退款已完成(<font color="#FF0000"><?php
                            $sql	= "select count(ebay_id) as cc from ebay_paypalrefund where ebay_user='$user' and status = 2";
                            $sql	= $dbcon->execute($sql);
                            $sql	= $dbcon->getResultArray($sql);
                            echo $sql[0]['cc'];
                            ?></font>)
    </span></a>
            <?php } //end of paypal?>

            <?php //wish ticket
            if($module == 'wishtickets'){ ?>
                <a href="wishtickets.php?module=wishtickets&status=0&action=客户问题未回复" <?php if($action=='客户问题未回复'){echo 'class="a_hover"';}?>>&nbsp;<span>客户问题未回复
    (<font color="#FF0000"><?php
                            $sql	= "select count(ebay_id) as cc from wishtickets where status='0' group by ticketid ";
                            $sql	= $dbcon->execute($sql);
                            $sql	= $dbcon->getResultArray($sql);
                            echo $sql[0]['cc'];
                            ?></font>)
    </span></a>


                <a href="wishtickets.php?module=wishtickets&status=1&action=客户问题已回复" <?php if($action=='客户问题已回复'){echo 'class="a_hover"';}?>>&nbsp;<span>客户问题已回复
    (<font color="#FF0000"><?php
                            $sql	= "select count(ebay_id) as cc from wishtickets where status='1' group by ticketid ";
                            $sql	= $dbcon->execute($sql);
                            $sql	= $dbcon->getResultArray($sql);
                            echo $sql[0]['cc'];
                            ?></font>)
    </span></a>

                <a href="wishtickets.php?module=wishtickets&status=2&action=客户问题已关闭" <?php if($action=='客户问题已关闭'){echo 'class="a_hover"';}?>>&nbsp;<span>客户问题已关闭(<font color="#FF0000"><?php
                            $sql	= "select count(ebay_id) as cc from wishtickets where status='2' group by ticketid ";
                            $sql	= $dbcon->execute($sql);
                            $sql	= $dbcon->getResultArray($sql);
                            echo $sql[0]['cc'];
                            ?></font>)
    </span></a>
            <?php } //end of paypal?>



            <?php
            //订单管理
            //*********************************************Order Start*********************************************************/
            $vieworderstatus=$_SESSION['vieworderstatus'];
            $vieworderstatusArr=explode(',',$vieworderstatus);
            if(!empty($vieworderstatus)){
                $vieworderstatusStr=" and id in($vieworderstatus) ";
                $sqlorderstatusStr=' and (a.ebay_status='.str_replace(','," or a.ebay_status=",$vieworderstatus).')';
            }else{
                $vieworderstatusStr='';
                $sqlorderstatusStr='';
            }

            if ($module == 'orders') {
                if (in_array('0', $vieworderstatusArr)) {
                    echo '<a href="orderindex.php?module=orders&ostatus=0&action=未付款">&nbsp;未付款</a>';
                    /*    $sql  = "select count(ebay_id) as cc from ebay_order as a where a.ebay_combine!='1'   and ($ebayacc) and a.ebay_status='0' $ebaystoreview ";
                        //echo $sql.'<br>';
               /*       $sqla = $dbcon->execute($sql);
                        $sql  = $dbcon->getResultArray($sqla);
                        $dbcon->free_result($sqla);*/
                    echo '<span data="0" class="getRealCount">(0+)</span>&nbsp;';
                }
                if (in_array('1', $vieworderstatusArr)) {
                    echo '<a href="orderindex.php?module=orders&ostatus=1&action=待处理">&nbsp;待处理</a>';
                    /*            $sql  = "select count(ebay_id) as cc from ebay_order as a where a.ebay_combine!='1'   and ($ebayacc) and a.ebay_status='1' $ebaystoreview ";
                                //echo $sql.'<br>';
                                $sqla = $dbcon->execute($sql);
                                $sql  = $dbcon->getResultArray($sqla);
                                $dbcon->free_result($sqla);*/
                    echo '<span data="1" class="getRealCount">(0+)</span>&nbsp;';
                    // echo '(<span class="getRealCount">?</span>)&nbsp;';
                }

                // 查询自定义 订单分类
                $ss  = "select id,name from ebay_topmenu where ebay_user='$user' $vieworderstatusStr and name!='' order by ordernumber";
                $ssa = $dbcon->execute($ss);
                $ss  = $dbcon->getResultArray($ssa);
                $dbcon->free_result($ssa);
                for ($i = 0; $i < count($ss); $i++) {
                    $ssid   = $ss[$i]['id'];
                    $ssname = $ss[$i]['name'];
                    if (($i + 1) % 12 == 0) {
                        echo '<br><br><b style="white-space:nowrap;"></b>';
                    }
                    if($ssid!=1731){
                        echo '<a href="orderindex.php?module=orders&ostatus=' . $ssid . '&action=' . $ssname . '&sortstatus=0">&nbsp;' . $ssname . '</a>';
                        /* $sql  = "select count(ebay_id) as cc from ebay_order as a where ($ebayacc) and a.ebay_status='$ssid' $ebaystoreview and a.ebay_combine!='1'";
                         //echo $sql.'<br>';
                         $sqla = $dbcon->execute($sql);
                         $sql  = $dbcon->getResultArray($sqla);*/
                        echo '<span data="'.$ssid.'" class="getRealCount">(<b>0+</b>)</span>&nbsp;';
                    }else{
                        echo '<a href="orderindex.php?module=orders&ostatus=' . $ssid . '&action=' . $ssname . '&sortstatus=0">&nbsp;<span>' . $ssname .'</span></a>';
                    }

                }

                if (in_array('0', $vieworderstatusArr)) {
                    echo '<a href="orderindex.php?module=orders&ostatus=2&action=已经发货">&nbsp;<span>已经发货</span></a>';
                }
                ?>
                <br><br><b style="white-space:nowrap;">Actions:&nbsp;&nbsp;</b>
                <a href="orderload.php?module=orders&action=同步订单">eBay同步</a>
                <a href="orderloadsmt.php?module=orders&action=速卖通同步">速卖通同步</a>
                <a href="amazonorderload.php?module=orders&action=同步订单">Amazon同步</a>
                <a href="magentosysorders.php?module=orders&action=同步订单">Magento线下同步</a>
                <a href="paypalorerload.php?module=orders&action=同步订单">Paypal线下同步</a>
                <a href="wishorderload.php?module=orders&action=WISH同步订单">WISH同步</a>
                <a href="cdiscountorderload.php?module=orders&action=Cdiscount同步">Cdiscount同步</a>
                <a href="priceministerorderload.php?module=orders&action=Priceminister同步">Priceminister同步</a>
                <a href="scanorder_baoiao.php?module=orders&action=Loading Orders">订单扫描</a>
                <a href="s_auditorder.php?module=orders&action=Loading Orders">核对查询</a>
                <a href="orderloadtj.php?module=orders&ostatus=1&action=数据统计">报表</a>
                <?php
            }
            // ******END of Order***********************************************************************************END of Order
            ?>


            <?php //<!-- 发信管理 -->
            if($module == 'mail'){ ?>
                <a href="feedbacksys.php?module=mail&action=好评加载" class="<?php if($_REQUEST['action']=='好评加载'){echo 'a_hover';};?>">&nbsp;<span>好评加载</span></a>
                <a href="feedback.php?module=mail&action=好评管理" class="<?php if($_REQUEST['action']=='好评管理'){echo 'a_hover';};?>">&nbsp;<span>好评管理</span></a>
                <a href="messagetemplate.php?module=mail&action=模板管理" class="<?php if($_REQUEST['action']=='模板管理'){echo 'a_hover';};?>">&nbsp;<span>信件模板管理</span></a>
                <a href="fahuo.php?module=mail&action=发货流程" class="<?php if($_REQUEST['action']=='发货流程'){echo 'a_hover';};?>">&nbsp;<span>发货流程</span></a>
                <a href="mailorderindex.php?module=mail&action=待发信订单&mstatus=0" class="<?php if($_REQUEST['action']=='待发信订单'){echo 'a_hover';};?>">&nbsp;<span>待发信订单
                        <!--(<font color="#FF0000">
    <?php
                        /*    $sql		= "select count(ebay_id) as ccct from ebay_order where (mailstatus='0' or mailstatus ='') and ebay_status='2'  and ebay_combine!='1' and  ebay_user='$user'";
                            $total		= $dbcon->getResultArrayBySql($sql);
                            echo $total[0]['ccct'];
                            $dbcon->free_result($query);
                            */?></font>)</span>--></a>
                <a href="mailorderindex.php?module=mail&action=正在发信订单&mstatus=1" class="<?php if($_REQUEST['action']=='正在发信订单'){echo 'a_hover';};?>">&nbsp;<span>正在发信订单
                        <!--(<font color="#FF0000">
    <?php
                        /*    $sql		= "select count(ebay_id) as ccct from ebay_order where mailstatus='1' and ebay_status='2'  and ebay_combine!='1' and  ebay_user='$user'";
                            $total		= $dbcon->getResultArrayBySql($sql);
                            echo $total[0]['ccct'];
                            $dbcon->free_result($query);
                            */?></font>)</span>--></a>

                <a href="mailorderindex.php?module=mail&action=已结束发信订单&mstatus=2"  class="<?php if($_REQUEST['action']=='已结束发信订单'){echo 'a_hover';};?>">&nbsp;<span>已结束发信订单</a>
                <a href="mailrun.php?module=mail&action=信件检查" class="<?php if($_REQUEST['action']=='信件检查'){echo 'a_hover';};?>">&nbsp;<span>信件检查</span></a>
                <a href="ebaymarktlog.php?module=mail&action=标发出处理" class="<?php if($_REQUEST['action']=='标发出处理'){echo 'a_hover';};?>">标发出处理</a>
                <a href="mailsku.php?module=mail&action=特殊SKU发信" class="<?php if($_REQUEST['action']=='特殊SKU发信'){echo 'a_hover';};?>">特殊SKU发信</a>
                <a href="mailset.php?module=mail&action=特殊SKU设置" class="<?php if($_REQUEST['action']=='特殊SKU设置'){echo 'a_hover';};?>">特殊SKU设置</a>
            <?php } ?>


            <?php //纠纷
            if($module == 'dispute'){ ?>
                <a href="dispute_index.php?module=dispute&action=售后纠纷&mstatus=0" <?php if($action=='售后纠纷'){echo ' class="a_hover"';}?>>&nbsp;<span>售后纠纷</span></a>
                <a href="autorefund.php?module=dispute&action=自动退款&type=1" <?php if($action=='自动退款'){echo ' class="a_hover"';}?>>自动退款</a>
                <a href="dispute_return.php?module=dispute&action=Return&mstatus=0" <?php if($action=='Return'){echo ' class="a_hover"';}?>>&nbsp;<span>Return</span></a>
                <a href="dispute_import.php?module=dispute&action=同步纠纷" <?php if($action=='同步纠纷'){echo ' class="a_hover"';}?>>&nbsp;<span>同步纠纷</span></a>
                <a href="salesmanual.php?module=dispute&action=售后手册" <?php if($action=='售后手册'){echo ' class="a_hover"';}?>>&nbsp;<span>售后手册</span></a>
            <?php } ?>



            <?php if($module == 'finance'){ ?>
                <?php if(in_array("ebay_sale_statistics",$cpower)){ ?>
                    <a href="ordertongji2.php?module=finance&action=销售额统计&type=1">&nbsp;<span>销售额统计</span></a>
                <?php } ?>
		<?php if(in_array("paypal_sale_statistics",$cpower)){ ?>
                    <a href="paypaltindex.php?module=finance&action=Paypal销售额统计">&nbsp;<span>Paypal销售额统计</span></a>
                <?php } ?>
		<?php if(in_array("paypal_table_view",$cpower)){ ?>
                    <a href="paypaldetail.php?module=finance&action=Paypal明细查看">&nbsp;<span>Paypal明细查看</span></a>
                <?php } ?>
		<?php if(in_array("ebay_table_view",$cpower)){ ?>
                    <a href="ebaydetail.php?module=finance&action=eBay费用明细查看">&nbsp;<span>eBay明细查看</span></a>
                <?php } ?>
		<?php if(in_array("product_sale_analyse",$cpower)){ ?>
                    <a href="productprofit.php?module=finance&action= 产品销售分析">&nbsp;<span>产品销售分析</span></a>
                <?php } ?>
		<?php if(in_array("synchronous_paypal_sale",$cpower)){ ?>
                    <a href="paypalimport.php?module=finance&action=同步Paypal销售额">&nbsp;<span>同步Paypal销售额</span></a>
                <?php } ?>
		<?php if(in_array("synchronous_ebay_fee",$cpower)){ ?>
                    <a href="ebayimport.php?module=finance&action=同步ebay费用">&nbsp;<span>同步ebay费用</span></a>
                <?php } ?>
		<?php if(in_array("sale_profit_sum_table",$cpower)){ ?>
                    <a href="ordertongji.php?module=finance&action=销售/利润汇总表">&nbsp;<span>销售/利润汇总表</span></a>
                <?php } ?>
		<?php if(in_array("orders_profit_unrecognize",$cpower)){ ?>
                    <a href="orderindexlr.php?module=finance&action=订单利润未确认&ostatus=100&profitstatus=0">&nbsp;<span>订单利润未确认
			(<font color="#FF0000"><?php
                                $sql	= "select count(ebay_id) as cc from ebay_order where ebay_user='$user' and ebay_combine!='1'  and ebay_status='2' and profitstatus=0 ";
                                if(strstr($_GET['action'],'订单利润')!==false){
                                    $sql	= $dbcon->execute($sql);
                                    $sql	= $dbcon->getResultArray($sql);
                                    echo $sql[0]['cc'];
                                }
                                ?></font>)
			</span></a>
                <?php } ?>
		<?php if(in_array("orders_profit_recognize",$cpower)){ ?>
                    <a href="orderindexlred.php?module=finance&action=订单利润已确认&ostatus=100&profitstatus=1">&nbsp;<span>订单利润已确认
                            <!--(<font color="#FF0000"><?php
                            //$sql	= "select count(ebay_id) as cc from ebay_order where ebay_user='$user' and ebay_combine!='1'  and ebay_status='2' and profitstatus=1 ";
                            //$sql	= $dbcon->execute($sql);
                            //$sql	= $dbcon->getResultArray($sql);
                            //echo $sql[0]['cc'];
                            ?></font>)-->
			</span></a>
                <?php } ?>
        <?php if(in_array("sale_table",$cpower)){?>
                    <a href="sale_to_table.php?module=finance&table=1&action=销售明细-按付款时间">&nbsp;<span>销售明细-按付款时间</span></a>
                <?php }?>

        <?php if(in_array("sale_table_bytime",$cpower)){?>
                    <a href="sale_to_table.php?module=finance&table=2&action=销售明细-按确认时间">&nbsp;<span>销售明细-按确认时间</span></a>
                <?php }?>

            <?php } ?>


            <?php if($module == 'zencart'){ ?>
            <a href="zenorderindex.php?module=zencart&action=ZenCart所有订单&ostatus=100">&nbsp;<span>ZenCart所有订单
    (<font color="#FF0000"><?php
                        $sql	= "select count(ebay_id) as cc from ebay_order where ebay_user='$user' and ebay_combine!='1' and ordertype ='1'  and (ebay_status='1' or ebay_status='2' or ebay_status='3' or ebay_status='4' or ebay_status='5' or ebay_status='6' or ebay_status='7' or ebay_status='8')";
                        $sql	= $dbcon->execute($sql);
                        $sql	= $dbcon->getResultArray($sql);
                        echo $sql[0]['cc'];
                        ?></font>)</span></a>


    <a href="zenorderindex.php?module=zencart&action=ZenCart未处理订单&ostatus=1">&nbsp;<span>ZenCart未处理订单

    (<font color="#FF0000"><?php
                $sql	= "select count(ebay_id) as cc from ebay_order where ebay_user='$user' and ebay_combine!='1' and ordertype ='1'  and ebay_status='1'";
                $sql	= $dbcon->execute($sql);
                $sql	= $dbcon->getResultArray($sql);
                echo $sql[0]['cc'];
                ?></font>)</span>
    </a>
    <a href="zenorderindex.php?module=zencart&action=ZenCart已处理订单&ostatus=2">&nbsp;<span>ZenCart已处理订单

    (<font color="#FF0000"><?php
                $sql	= "select count(*) as cc from ebay_order where ebay_user='$user' and ebay_combine!='1' and ordertype ='1'  and ebay_status='2'";
                $sql	= $dbcon->execute($sql);
                $sql	= $dbcon->getResultArray($sql);
                echo $sql[0]['cc'];
                ?></font>)</span></a>

    <a href="zenorderindex.php?module=zencart&action=ZenCart待打印订单&ostatus=7">&nbsp;<span>ZenCart待打印订单

    (<font color="#FF0000"><?php
                $sql	= "select count(*) as cc from ebay_order where ebay_user='$user' and ebay_combine!='1' and ordertype ='1'  and ebay_status='7'";
                $sql	= $dbcon->execute($sql);
                $sql	= $dbcon->getResultArray($sql);
                echo $sql[0]['cc'];
                ?></font>)</span></a>

    <a href="zenorderindex.php?module=zencart&action=ZenCart已打印订单&ostatus=8">&nbsp;<span>ZenCart已打印订单

    (<font color="#FF0000"><?php
                $sql	= "select count(*) as cc from ebay_order where ebay_user='$user' and ebay_combine!='1' and ordertype ='1'  and ebay_status='8'";
                $sql	= $dbcon->execute($sql);
                $sql	= $dbcon->getResultArray($sql);
                echo $sql[0]['cc'];
                ?></font>)</span></a>

    <a href="zenorderindex.php?module=zencart&action=ZEN-CART待MARK SHIP&ostatus=3">&nbsp;<span>ZEN-CART待MARK SHIP

    (<font color="#FF0000"><?php
                $sql	= "select count(*) as cc from ebay_order where ebay_user='$user' and ebay_combine!='1' and ordertype ='1'  and ebay_status='3'";
                $sql	= $dbcon->execute($sql);
                $sql	= $dbcon->getResultArray($sql);
                echo $sql[0]['cc'];
                ?></font>)</span>

    <a href="zenorderindex.php?module=zencart&action=ZenCart退款订单&ostatus=4">&nbsp;<span>ZenCart退款订单

    (<font color="#FF0000"><?php
                $sql	= "select count(*) as cc from ebay_order where ebay_user='$user' and ebay_combine!='1' and ordertype ='1'  and ebay_status='4'";
                $sql	= $dbcon->execute($sql);
                $sql	= $dbcon->getResultArray($sql);
                echo $sql[0]['cc'];
                ?></font>)</span></a>

    <a href="zenorderindex.php?module=zencart&action=ZenCart取消订单&ostatus=5">&nbsp;<span>ZenCart取消订单

    (<font color="#FF0000"><?php
                $sql	= "select count(*) as cc from ebay_order where ebay_user='$user' and ebay_combine!='1' and ordertype ='1'  and ebay_status='5'";
                $sql	= $dbcon->execute($sql);
                $sql	= $dbcon->getResultArray($sql);
                echo $sql[0]['cc'];
                ?></font>)</span></a>

    <a href="zenorderindex.php?module=zencart&action=ZenCart缺货订单&ostatus=6">&nbsp;<span>ZenCart缺货订单

    (<font color="#FF0000"><?php
                $sql	= "select count(*) as cc from ebay_order where ebay_user='$user' and ebay_combine!='1' and ordertype ='1'  and ebay_status='6'";
                $sql	= $dbcon->execute($sql);
                $sql	= $dbcon->getResultArray($sql);
                echo $sql[0]['cc'];
                ?></font>)</span>
    </a>
    <a href="zenorderload.php?module=zencart&action=ZenCart订单同步">&nbsp;<span>ZenCart订单同步</span></a>



        <?php } ?>


        <!-- 采购管理 -->
        <?php if($module == 'purchase'){ ?>

            <!--
            <b style="white-space:nowrap;">供应商管理:&nbsp;&nbsp;</b>
            <a href="purchaseorderpay.php?module=purchase&action=供应商应付款">&nbsp;<span>供应商应付款</span></a>
            <br>
            <br>
            -->


            <b style="white-space:nowrap;">采购管理&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</b>

    <?php if(in_array("purchase_outstockorder",$cpower)){?>
                <a href="purchase_stock.php?module=purchase&action=备货采购">&nbsp;<span>备货采购</span></a>
                <a href="purchase_outstockorder.php?module=purchase&action=缺货采购">&nbsp;<span>缺货采购</span></a>
            <?php } ?>

    <?php if(in_array("purchase_newplan",$cpower)){?>
                <img src="images/20100916225534661.png" alt="" width="25" height="25"><a href="purchase_newplan.php?module=purchase&action=新建采购计划">&nbsp;<span>新建采购计划</span></a>
            <?php } ?>


    <?php if(in_array("purchase_newplanorders",$cpower)){?>
                <img src="images/20100916225534661.png" alt="" width="25" height="25"><a href="purchase_newplanorders.php?module=purchase&action=采购计划单">&nbsp;<span>采购计划单</span></a>
            <?php } ?>
    <?php if(in_array("purchase_instocking",$cpower)){?>
                <img src="images/20100916225534661.png" alt="" width="25" height="25"><a href="instockhistory.php?module=purchase&action=采购计划单">&nbsp;<span>预定中订单历史数据</span></a>
            <?php } ?>
    <?php if(in_array("purchase_productstockalarm",$cpower)){?>
                <img src="images/20100916225534661.png" alt="" width="25" height="25"><a href="productstockalarm.php?module=purchase&action=智能预警">&nbsp;<span>智能预警</span></a>
            <?php } ?>
    <?php if(in_array("system_factor",$cpower)||in_array("partner_view",$cpower)){?>
                <img src="images/20100916225534661.png" alt="" width="25" height="25"><a href="partner.php?module=purchase&action=供应商">&nbsp;<span>供应商</span></a>
            <?php } ?>
    <br>
            <br>

            <b style="white-space:nowrap;">采购转入库:&nbsp;&nbsp;</b>
    <?php if(in_array("purchase_instocking",$cpower)){?>
                <a href="purchase_instocking.php?module=purchase&action=采购中订单">&nbsp;<span>预定中订单</span></a>
            <?php } ?>

    <?php if(in_array("purchase_instock",$cpower)){?>
                <img src="images/20100916225534661.png" alt="" width="25" height="25">
                <a href="purchase_instock.php?module=purchase&action=入库单列表">&nbsp;<span>入库单列表</span></a>
            <?php } ?>


    <?php if(in_array("purchase_FinanceChecklist",$cpower)||in_array("purchase_FinanceCheck",$cpower)){?>
                <img src="images/20100916225534661.png" width="25" height="25"><a href="purchase_FinanceCheck.php?module=purchase&action=财务审核">&nbsp;<span>财务审核</span></a>
            <?php } ?>

    <?php if(in_array("purchase_qcorders",$cpower)){?>
                <img src="images/20100916225534661.png" alt="" width="25" height="25"><a href="purchase_qcorders.php?module=purchase&action=质检审核">&nbsp;<span>质检审核</span></a>
            <?php } ?>

    <?php if(in_array("purchase_qcordersyc",$cpower)){?>
                <img src="images/20100916225534661.png" width="25" height="25"><a href="purchase_qcordersyc.php?module=purchase&action=异常入库单">&nbsp;<span>异常入库单</span></a>
            <?php } ?>

    <?php if(in_array("purchase_completeorders",$cpower)){?>
                <img src="images/20100916225534661.png" width="25" height="25"><a href="purchase_completeorders.php?module=purchase&action=已完成">&nbsp;<span>正常已完成</span></a>

            <?php } ?>
    <?php if(in_array("purchase_completeorders",$cpower)){?>
                <a href="purchase_qcoverorders.php?module=purchase&action=有异常完成">&nbsp;<span>有异常完成</span></a>

            <?php } ?>
    <br>


        <?php } ?>

        <?php if($module == 'package'){ ?>
            <?php if(in_array("package_newplan",$cpower)){?>
                <a class="<?php if($action=='添加产品') echo 'a_hover';?>" href="package_newplanadds.php?module=package&hidedata=8&action=添加产品">&nbsp;<span>添加产品</span></a>
                <img src="images/20100916225534661.png" alt="" width="12" height="12"><a class="<?php if($action=='发货计划') echo 'a_hover';?>" href="package_newplan.php?module=package&action=发货计划">&nbsp;<span>发货计划</span></a>
                <img src="images/20100916225534661.png" alt="" width="12" height="12"><a class="<?php if($action=='等待配货') echo 'a_hover';?>" href="package_newplan_whs.php?module=package&action=等待配货">&nbsp;<span>等待配货</span></a>
                <img src="images/20100916225534661.png" alt="" width="12" height="12"><a class="<?php if($action=='等待装箱') echo 'a_hover';?>" href="package_newplan_wpk.php?module=package&action=等待装箱">&nbsp;<span>等待装箱</span></a>
            <?php } ?>
        <?php if(in_array("packagelist_drafe",$cpower)){?>
                <a class="<?php if($action=='等待审核') echo 'a_hover';?>" href="packagelist_drafe.php?module=package&action=等待审核">&nbsp;<span>等待审核</span></a>
            <?php } ?>
        <?php if(in_array("packagelist_submit",$cpower)){?>
                <img src="images/20100916225534661.png" alt="" width="12" height="12"><a class="<?php if($action=='等待扫描') echo 'a_hover';?>" href="packagelist_submit.php?module=package&action=等待扫描">&nbsp;<span>等待扫描</span></a>
            <?php } ?>
        <?php if(in_array("packagelist_transit",$cpower)){?>
                <img src="images/20100916225534661.png" alt="" width="12" height="12"><a class="<?php if($action=='在途订单') echo 'a_hover';?>" href="packagelist_transit.php?module=package&action=在途订单">&nbsp;<span>在途订单</span></a>
            <?php } ?>
        <?php if(in_array("packagelist_shelved",$cpower)){?>
                <img src="images/20100916225534661.png" alt="" width="12" height="12"><a class="<?php if($action=='已上架订单') echo 'a_hover';?>" href="packagelist_shelved.php?module=package&action=已上架订单">&nbsp;<span>已上架订单</span></a>
            <?php } ?>
        <?php if(in_array("packagelist_void",$cpower)){?>
                <img src="images/20100916225534661.png" alt="" width="12" height="12"><a class="<?php if($action=='作废订单') echo 'a_hover';?>" href="packagelist_void.php?module=package&action=作废订单">&nbsp;<span>作废订单</span></a>
            <?php } ?>

        <?php } ?>

        <?php if($module == 'data_audit'){ ?>
            <img src="images/20100916225534661.png" alt="" width="12" height="12"><a class="<?php if($action=='待执行') echo 'a_hover';?>" href="dataaudit.php?module=data_audit&action=待执行&auditstatus=1">&nbsp;<span>待执行</span></a>
            <img src="images/20100916225534661.png" alt="" width="12" height="12"><a class="<?php if($action=='执行成功') echo 'a_hover';?>" href="dataaudit.php?module=data_audit&action=执行成功&auditstatus=2">&nbsp;<span>执行成功</span></a>
            <img src="images/20100916225534661.png" alt="" width="12" height="12"><a class="<?php if($action=='执行失败') echo 'a_hover';?>" href="dataaudit.php?module=data_audit&action=执行失败&auditstatus=3">&nbsp;<span>执行失败</span></a>
            <img src="images/20100916225534661.png" alt="" width="12" height="12"><a class="<?php if($action=='拒绝执行') echo 'a_hover';?>" href="dataaudit.php?module=data_audit&action=拒绝执行&auditstatus=4">&nbsp;<span>拒绝执行</span></a>
        <?php } ?>


        <?php if($module == 'goods'){  //新品添加流程化控制
            ?>

            <?php
            if(in_array('goods_wish',$cpower)){
                ?>
                <a class="<?php if($auditted==10&&$_GET['action']=='wish新品') echo 'a_hover';?>" href="kaifa_wish.php?module=goods&auditted=10&action=wish新品">&nbsp;
                <span>wish新品(<font style="color:#FF8888">
                        <?php
                        $sql	= "select count(goods_id) as cc from ebay_goods_audit where auditted ='10' and copyright!=2 ";//and ebay_user='$user'";
                        $sqla	= $dbcon->execute($sql);
                        $sql	= $dbcon->getResultArray($sqla);
                        echo $sql[0]['cc'];
                        $dbcon->free_result($sqla);

                        ?>
                    </font>)
        </span></a>

            <?php } ?>

        <?php
            if(in_array('goods_wish',$cpower)){
                ?>
                <a class="<?php if($auditted==10&&$_GET['action']=='wish新品未侵权') echo 'a_hover';?>" href="kaifa_wish_copyright.php?module=goods&auditted=10&action=wish新品未侵权">&nbsp;
                <span>wish新品未侵权(<font style="color:#FF8888">
                        <?php
                        $sql	= "select count(goods_id) as cc from ebay_goods_audit where auditted ='10' and copyright=2 ";//and ebay_user='$user'";
                        $sqla	= $dbcon->execute($sql);
                        $sql	= $dbcon->getResultArray($sqla);
                        echo $sql[0]['cc'];
                        $dbcon->free_result($sqla);

                        ?>
                    </font>)
        </span></a>

            <?php } ?>

        <?php
            if(in_array('goods_5',$cpower)){
                ?>
                <a class="<?php if($auditted==5) echo 'a_hover';?>" href="kaifa_cy.php?module=goods&auditted=5&action=新创意">&nbsp;
                <span>新创意(<font style="color:#FF8888">
                        <?php
                        $sql	= "select count(goods_id) as cc from ebay_goods_audit where auditted ='5'";//and ebay_user='$user'";
                        $sqla	= $dbcon->execute($sql);
                        $sql	= $dbcon->getResultArray($sqla);
                        echo $sql[0]['cc'];
                        $dbcon->free_result($sqla);

                        ?>
                    </font>)
        </span></a>
            <?php } ?>
    <!--初次审核-->
        <?php
            if(in_array('goods_7',$cpower)){
                ?>
                <a class="<?php if($auditted==7) echo 'a_hover';?>" href="kaifa_xq.php?module=goods&auditted=7&action=初次审核">&nbsp;
    <span>初次审核(<font style="color:#FF8888">
            <?php
            $sql	= "select count(goods_id) as cc from ebay_goods_audit where auditted ='7'";//and ebay_user='$user'";
            $sqla	= $dbcon->execute($sql);
            $sql	= $dbcon->getResultArray($sqla);
            echo $sql[0]['cc'];
            $dbcon->free_result($sqla);
            ?></font>)</span></a>
            <?php } ?>

        <?php //<!--开发中-->
            if(in_array('goods_0',$cpower)) {
                ?>
                <a class="<?php if ($auditted == 0)
                    echo 'a_hover'; ?>" href="addgoodsindex.php?module=goods&auditted=0&action=开发中">&nbsp;
                <span>开发中(<font style="color:#FF8888">
                        <?php
                        $sql  = "select count(goods_id) as cc from ebay_goods_audit where auditted ='0'";//and ebay_user='$user'";
                        $sqla = $dbcon->execute($sql);
                        $sql  = $dbcon->getResultArray($sqla);
                        echo $sql[0]['cc'];
                        $dbcon->free_result($sqla);

                        ?>
                    </font>)</span></a>
                <?php
            }
            ?>
        <?php //<!--图-->
            if(in_array('goods_8',$cpower)) {
                ?>
                <a class="<?php if($auditted==8) echo 'a_hover';?>" href="addgoodsindex.php?module=goods&auditted=8&action=图片处理">&nbsp;
    <span>图片处理(<font style="color:#FF8888">
            <?php
            $sql	= "select count(goods_id) as cc from ebay_goods_audit where auditted ='8'";//and ebay_user='$user'";
            $sqla	= $dbcon->execute($sql);
            $sql	= $dbcon->getResultArray($sqla);
            echo $sql[0]['cc'];
            $dbcon->free_result($sqla);

            ?>
        </font>)</span></a>

            <?php } ?>

        <?php  //等待终审
            if(in_array('goods_2',$cpower)){
                ?>
                <a class="<?php if($auditted==2) echo 'a_hover';?>" href="addgoodsindex.php?module=goods&auditted=2&action=待审批货品">&nbsp;
                <span>审批(<font style="color:#FF8888">
        <?php
        $sql	= "select count(goods_id) as cc from ebay_goods_audit where auditted = '2' ";//and ebay_user='$user'";
        $sqla	= $dbcon->execute($sql);
        $sql	= $dbcon->getResultArray($sqla);
        echo $sql[0]['cc'];
        $dbcon->free_result($sqla);
        ?>
        </font>)</span>
        </a>
            <?php } ?>

        <?php //审核未通过   原来为  分配执行
            if(in_array('goods_3',$cpower)){
                ?>
                <a  class="<?php if($auditted==3) echo 'a_hover';?>" href="addgoodsindex.php?module=goods&auditted=3&action=未通过">&nbsp;
            <span>未通过(<font style="color:#FF8888">
                    <?php
                    $sql	= "select count(goods_id) as cc from ebay_goods_audit where auditted = '3' ";//and ebay_user='$user'";
                    $sqla	= $dbcon->execute($sql);
                    $sql	= $dbcon->getResultArray($sqla);
                    echo $sql[0]['cc'];
                    //释放mysql 系统资源
                    $dbcon->free_result($sqla);
                    ?>
                </font>)</span>
        </a>
            <?php }?>

        <?php //回收站   原来为  采样询价
            if(in_array('goods_4',$cpower)){
                ?>

                <a class="<?php if($auditted==4) echo 'a_hover';?>" href="addgoodsindex.php?module=goods&auditted=4&action=回收站">&nbsp;
                <span>回收站(<font style="color:#FF8888">
                    <?php
                    $sql	= "select count(goods_id) as cc from ebay_goods_audit where auditted = '4' or auditted ='6'";//and ebay_user='$user'";
                    $sqla	= $dbcon->execute($sql);
                    $sql	= $dbcon->getResultArray($sqla);
                    echo $sql[0]['cc'];
                    //释放mysql 系统资源
                    $dbcon->free_result($sqla);
                    ?>
                </font>)</span>
            </a>
            <?php }?>
        <a class="<?php if($auditted==99) echo 'a_hover';?>" href="kfplanlist.php?module=goods&auditted=99&action=开发人员任务列表">&nbsp;<span>开发人员任务列表</span></a>
            <a class="<?php if($auditted==98) echo 'a_hover';?>" href="speedkfskus.php?module=goods&auditted=98&action=完善货品资料">&nbsp;<span>完善货品资料</span></a>
        <?php } ?>


        <?php
        if($module == 'list'){ ?>
            <a   <?php if($action=="在线物品")echo 'class="a_hover"' ?>  href="listing.php?module=list&action=在线物品&status=0">&nbsp;<span>在线物品
    (<font color="#FF0000">
    <?php
    $sql	= "select count(*) as cc from ebay_list where status='0' and ebay_user='$user' ";
    $sql	= $dbcon->execute($sql);
    $sql	= $dbcon->getResultArray($sql);
    echo $sql[0]['cc'];
    ?>
    </font>)</span></a>



            <a <?php if($action=="结束物品")echo 'class="a_hover"' ?> href="listing.php?module=list&action=结束物品&status=1">&nbsp;<span>结束物品
    (<font color="#FF0000">
    <?php
    $sql	= "select count(*) as cc from ebay_list where status='1' and ebay_user='$user' ";
    $sql	= $dbcon->execute($sql);
    $sql	= $dbcon->getResultArray($sql);
    echo $sql[0]['cc'];
    ?>
    </font>)</span></a>

            <a  <?php if($action=="导入在线物品")echo 'class="a_hover"' ?> href="listingimportproducts.php?module=list&action=导入在线物品&status=1">&nbsp;<span>导入在线物品</span></a>
            <a  <?php if($action=="操作日志")echo 'class="a_hover"' ?> href="listlog.php?module=list&action=操作日志&status=1">&nbsp;<span>操作日志</span></a>
            <a  <?php if($action=="自动下架")echo 'class="a_hover"' ?> href="ebayenditem.php?module=list&action=自动下架&status=1">&nbsp;<span>自动下架</span></a>
            <a  <?php if($action=="下架日志")echo 'class="a_hover"' ?> href="include/listunsoldlogs.php?module=list&action=下架日志&status=1" target="_blank">&nbsp;<span>下架日志</span></a>
            <a  <?php if($action=="ebay通知日志")echo 'class="a_hover"' ?> href="eventlog.php?module=list&action=ebay通知日志&status=1">&nbsp;<span>ebay通知日志</span></a>
            <a  <?php if($action=="在线调零")echo 'class="a_hover"' ?> href="setStocklist.php?module=list&action=在线调零&status=1">&nbsp;<span>在线调零</span></a>



        <?php }?>


        <!-- 系统配置 -->
        <?php /*if($module == 'system'){ ?>

    <?php if(in_array("system_ebayaccount",$cpower)){?>
    <a href="systemebay.php?module=system&action=eBay帐号管理">&nbsp;<span>eBay帐号管理</span></a>
    <?php } ?>
    <?php if(in_array("system_amazonaccount",$cpower)){?>
    <a href="systeamazonaccount.php?module=system&action=Amazon帐号管理">&nbsp;<span>Amazon帐号管理</span></a>
    <?php } ?>

    <?php if(in_array("system_zencartaccount",$cpower)){?>
    <a href="systemzencart.php?module=system&action=ZenCart帐号管理">&nbsp;<span>ZenCart帐号管理</span></a>
    <?php } ?>

    <?php if(in_array("system_magentoaccount",$cpower)){?>
    <a href="systemmagento.php?module=system&action=Magento帐号管理">&nbsp;<span>Magento帐号管理</span></a>
    <?php } ?>

    <?php if(in_array("system_papalaccount",$cpower)){?>
    <a href="paypalindex.php?module=system&action=paypal帐号管理">&nbsp;<span>Paypal帐号管理</span></a>
    <?php } ?>

    <?php if(in_array("system_modpass",$cpower)){?>
    <a href="systemuserpass.php?module=system&action=密码修改">&nbsp;<span>密码修改</span></a>
    <?php } ?>

    <?php if(in_array("system_ordertype",$cpower)){?>
    <a href="systemuordertype.php?module=system&action=订单类型管理">&nbsp;<span>订单类型管理</span></a>
    <?php } ?>

    <?php if(in_array("sysetm_currency",$cpower)){?>
    <a href="systemrates.php?module=system&action=汇率设置">&nbsp;<span>汇率设置</span></a>
    <?php } ?>

    <?php if(in_array("system_users",$cpower)){?>
    <a href="systemusers.php?module=system&action=用户管理">&nbsp;<span>用户管理</span></a>
    <?php } ?>

    <?php if(in_array("sytem_carrier",$cpower)){?>
    <a href="systemcarrier.php?module=system&action=发货方式">&nbsp;<span>发货方式</span></a>
    <?php } ?>

    <?php if(in_array("system_region",$cpower)){?>
    <a href="systemcountrys.php?module=system&action=地区列表">&nbsp;<span>地区列表</span></a>
    <?php } ?>

    <?php if(in_array("system_users",$cpower)){?>
    <a href="systemlog.php?module=system&action=操作日志">&nbsp;<span>日志管理</span></a>
    <?php } ?>
	<?php if(in_array("system_users",$cpower)){?>
    <a href="systemchecklog.php?module=system&action=操作日志">&nbsp;<span>日志文件</span></a>
    <?php } ?>
    <?php if(in_array("control_add",$cpower)){?>
    <a href="control_add.php?module=system&action=达标率标准设置">&nbsp;<span>达标率标准设置</span></a>
    <?php } ?>
    <?php if(in_array("system_orderstatus",$cpower)){?>
    <br><br>
    <a href="systemcustommenu.php?module=system&action=订单分类">&nbsp;<span>订单分类</span></a>
    <?php } ?>

    <?php if(in_array("system_config",$cpower)){?>
    <!--<a href="systemordershide.php?module=system&action=订单清理">&nbsp;<span>订单清理</span></a> -->
    <a href="systemconfig.php?module=system&action=系统配置">&nbsp;<span>系统配置</span></a>
    <?php } ?>

    <?php if(in_array("system_goodsstatus",$cpower)){?>
    <a href="systemgoodsstatus.php?module=system&action=物品状态">&nbsp;<span>物品状态</span></a>
    <?php } ?>

	<?php if(in_array("system_develop_config",$cpower)){?>
    <a href="systemkfgoods.php?module=system&action=开发设置">&nbsp;<span>开发设置</span></a>
    <?php } ?>

	<?php if(in_array("system_factor",$cpower)){?>
	<a href="partner.php?module=system&action=供应商">&nbsp;<span>供应商</span></a>
	<?php } ?>

   <?php if(in_array("platform_cost_config",$cpower)){?>
     <a href="goodspitai.php?module=system&action=平台费用设置">&nbsp;<span>平台费用设置</span></a>
   <?php } //end of 系统配置?>
   <a href="worktime.php?module=system&action=年工作时间段设置">&nbsp;<span>年工作时间段设置</span></a>

<?php } */?>

        <?php /*if($module == 'report'){ ?>
        <!--<a href="reportsales.php?module=report&action=销售收入统计表">&nbsp;<span>销售收入统计表</span></a>
        <a href="reportsales01.php?module=report&action=订单数量/销售额走势">&nbsp;<span>订单数量/销售额走势表 </span></a>
        <a href="reportsales02.php?module=report&action=销售纯利报表">&nbsp;<span>销售纯利报表</span></a>
        <a href="reportsales03.php?module=report&action=销售纯利明细表">&nbsp;<span>销售纯利明细表</span></a>-->
         <?php if(in_array("control_rate",$cpower)){?>
        <a href="control_rate.php?module=report&action=发货达标率报表">&nbsp;<span>发货达标率报表</span></a>
         <?php } ?>
         <?php if(in_array("salerate",$cpower)){?>
           <a href="salerate.php?module=report&table=2&action=SKU销频涨跌明细表">&nbsp;<span>SKU销频涨跌明细表</span></a>
         <?php } ?>
         <?php if(in_array("sale_sku_dms",$cpower)){?>
           <a href="sale_sku_dms.php?module=report&table=2&action=SKU评估">&nbsp;<span>SKU评估</span></a>
         <?php } ?>
         <?php if(in_array("sku_trans_ratio2",$cpower)){?>
           <a href="sku_trans_ratio2.php?module=report&action=SKU转化率">&nbsp;<span>SKU转化率</span></a>
         <?php } ?>
       <?php } */?>

    </span>
    </div>

</div>
