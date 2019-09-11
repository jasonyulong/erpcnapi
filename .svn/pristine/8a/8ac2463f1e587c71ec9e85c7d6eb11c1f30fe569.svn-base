//2017-05-16从orderindexjs.php复制而来

function showOneStstusCount(that){

    var status=that.attr("data");
    var sclass=that.attr("class");
    if(sclass.indexOf('loading')!=-1){
        //console.log('正在加载....');
        return false; //正在加载! 别再点击了!
    }
    that.html('(<img src="cx.gif"/>)');
    that.addClass('loading');
    $.post(
        'toxls/ajax.php',
        {doaction:'ajaxgetstatuscount','status':status},
        function(data){
            data= $.trim(data);
            if(!isNaN(data)){
                that.html('('+data+')');
                that.removeClass('loading');
            }
        }
    )
}

function showAllStatusCounts(){
    $("span.getRealCount").each(function(){
        var that=$(this);
        showOneStstusCount(that);
    })
}

function SearchVal(val,arr){
    var len=arr.length,i=0;
    for(;i<len;i++){
        if(arr[i]==val){
            return i;
        }
    }
    return -1;
}

function check_all0(obj,cName)

{

    var checkboxs = document.getElementsByName(cName);

    for(var i=0;i<checkboxs.length;i++){

        if(checkboxs[i].checked == false){



            checkboxs[i].checked = true;

        }else{



            checkboxs[i].checked = true;

        }



    }

}



function check_all(obj,cName)

{

    var checkboxs = document.getElementsByName(cName);

    for(var i=0;i<checkboxs.length;i++){

        if(checkboxs[i].checked == false){



            checkboxs[i].checked = true;

        }else{



            checkboxs[i].checked = false;

        }



    }
    displayselect();

}



function combine(){




    var g		= 0;

    var bill=getSelectOrder();
    g=bill.split(',').length;

    if(bill == ""){



        alert("请选择订单号");

        return false;



    }



    if(g<=1){



        alert("合并订单最少需要选择两个或两个以上的订单！！！");

        return false;

    }





    if(confirm("确认合并吗，客户订单信息，将以第一个订单信息为准，确认？")){



        window.open("ordercombine.php?ordersn="+bill,"_blank");



    }



}















function ebaymarket(type){
    var bill=getSelectOrder();
    //g=bill.split(',').length;

    if(bill == ""){



        //	alert("请选择订单号");

        //	return false;



    }

    var alerstr		= '您确认将选中订单在ebay上标记发出吗?';

    if(type == 1){
        alerstr		= '你确认出库吗';
    }
    if(confirm(alerstr)){
        window.open("ordermarket.php?ordersn="+bill+"&type="+type,"_blank");
    }
}


function ebaymarketwish(type){
    var bill=getSelectOrder();

    if(bill == ""){



        // alert("请选择订单号");

        // return false;



    }

    var alerstr		= '您确认将选中订单在WISH上标记发出吗?';

    if(type == 1){
        alerstr		= '你确认出库吗';
    }
    if(confirm(alerstr)){
        window.open("ordermarketwish.php?ordersn="+bill+"&type="+type,"_blank");
    }
}





function ammarket(type){
    var bill=getSelectOrder();

    if(bill == ""){



    }

    var alerstr		= '您确认将选中订单在amaozn上标记发出吗?';

    if(type == 1){
        alerstr		= '你确认出库吗';
    }
    if(confirm(alerstr)){
        window.open("AMordermarket.php?ordersn="+bill+"&type="+type,"_blank");
    }
}


function ebaymarketCdicount(){
    var bill=getSelectOrder();
    if(bill == ""){
         alert("请选择订单号");
         return false;
    }

    var alerstr		= '您确认将选中订单在Cdiscount上标记发出吗?';

    if(confirm(alerstr)){
        window.open("ordermarketcdiscount.php?ordersn="+bill,"_blank");
    }

}

function ebaymarketPm(){
    var bill=getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }

    var alerstr		= '您确认将选中订单在priceminister上标记发出吗?';

    if(confirm(alerstr)){
        window.open("ordermarketpriceminister.php?ordersn="+bill,"_blank");
    }

}

function ebaymarketWm(){
    var bill=getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }

    var alerstr		= '您确认将选中订单在walmart上标记发出吗?';

    if(confirm(alerstr)){
        window.open("ordermarketwalmart.php?ordersn="+bill,"_blank");
    }
}

function ebaymarketJoom(){
    var bill=getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }

    var alerstr		= '您确认将选中订单在 Joom 上标记发出吗?';

    if(confirm(alerstr)){
        window.open("./t.php?s=/Order/Joom/market&ordersn="+bill,"_blank");
    }
}

function ebaymarketLazada(){
    var bill=getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }

    var alerstr     = '您确认将选中订单在 Lazada 上标记发出吗?';

    if(confirm(alerstr)){
        window.open("./t.php?s=/Order/Lazada/market&ordersn="+bill,"_blank");
    }
}
function ebaymarketLinio(){
    var bill=getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }

    var alerstr     = '您确认将选中的 LINIO 订单标记为Ready To Ship吗?';

    if(confirm(alerstr)){
        window.open("./t.php?s=/Order/Linio/market&ordersn="+bill,"_blank");
    }
}

function ebaymarketShopee(){
    var bill=getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    var alerstr = '您确认将选中订单在 SHOPEE 上标记发出吗？';
    if(confirm(alerstr)){
        window.open("./t.php?s=/Order/Shopee/market&ordersn="+bill,"_blank");
    }
}

function selectFnTmarket(that){
    var va=document.getElementById("allmarket").value;

    if(''==va){ return;}
    if('1'==va){
        ebaymarket(0);return;
    }

    if('2'==va){
        ebaymarketwish(0);return;
    }

    if('3'==va){
        ebaymarketsmt(0);return;
    }

    if('4'==va){
        ammarket(0);return;
    }

    if('5'==va){
        ebaymarketCdicount(0);return;
    }
    if('6'==va){
        ebaymarketPm(0);return;
    }
    if('7'==va){
        ebaymarketWm(0);
        return;
    }

    if('8'==va){
        ebaymarketJoom(0);
        return;
    }

    if('9'==va){
        ebaymarketLazada(0);
    }

    if('10'==va){
        ebaymarketLinio(0);
    }

    if('11'==va){
        ebaymarketShopee(0);
    }

    return ;
}

/*
function searchorder(){
    var ebay_ordertype	 		= getCshu(document.getElementById('ebay_ordertype').value,'ebay_ordertype');
    var fprice	 		        = getCshu(document.getElementById('fprice').value,'fprice');
    var eprice	 		        = getCshu(document.getElementById('eprice').value,'eprice');
    var fweight	 		        = getCshu(document.getElementById('fweight').value,'fweight');
    var eweight 		        = getCshu(document.getElementById('eweight').value,'eweight');
    var isnote	 				= getCshu(document.getElementById('isnote').value,'isnote');
    var ebay_site	 			= getCshu(document.getElementById('ebay_site').value,'ebay_site');
    var searchtype 				= document.getElementById('searchtype').value;
    var keys	 				= document.getElementById('keys').value;
    var country	 				= getCshu(document.getElementById('country').value,'country');
    var acc	 					= getCshu(document.getElementById('acc').value,'account');
    var Shipping	 			= getCshu(document.getElementById('Shipping').value,'Shipping');
    var Shipping1	 			= getCshu(document.getElementById('Shipping1').value,'Shipping1');

    var stockstatus	 			= getCshu(document.getElementById('stockstatus').value,'stockstatus');

    var start			        = getCshu(document.getElementById('start').value,'start');
    var end				        = getCshu(document.getElementById('end').value,'end');

    var paystart			        = getCshu(document.getElementById('paystart').value,'paystart');
    var payend				        = getCshu(document.getElementById('payend').value,'payend');


    var scanstart			    = getCshu(document.getElementById('scanstart').value,'scanstart');
    var scanend				    = getCshu(document.getElementById('scanend').value,'scanend');

    var status			        = getCshu(document.getElementById('status').value,'status');
    var erp_op_id			    = getCshu(document.getElementById('erp_op_id').value,'erp_op_id');


    var isprint			        = getCshu(document.getElementById('isprint').value,'isprint');
    var stoptime               = getCshu(document.getElementById('stop_time').value,'stoptime');
    var input_warehouse               = getCshu(document.getElementById('input_warehouse').value,'input_warehouse');
    var istrue		            = '';
    var platform                = getCshu(document.getElementById('platform').value,'platform');

    //已处理订单
    var processed_status        = getCshu(document.getElementById('processed_status').value,'processed_status');


    var url='orderindex.php?module=orders&action=<?php echo $_REQUEST['action'];?>&ostatus=<?php echo $ostatus;?>&searchtype=';
    url+=searchtype+'&keys='+encodeURIComponent(keys)+stoptime+country+Shipping+Shipping1+acc+isprint+start+end+isnote;
    url+="&pagesize=<?php echo $pagesize;?>"+status+ebay_ordertype+fprice+eprice+fweight+eweight+ebay_site+stockstatus+isprint+erp_op_id+input_warehouse+platform+processed_status;
    url+=scanend+scanstart;
    location.href=url;
}
*/

function getCshu(val,str){
    val= val.replace(/^\s*|\s*$/g, "");
    return val===""?'':'&'+str+'='+val;
}

function sdformat(){

    var bill=getSelectOrder();

    var country 	= document.getElementById('country').value;

    //	window.open("ordertoexcelst.php?bill="+bill,"_blank");

    window.open("allpacklist.php?ordersn="+bill+"&country="+country,"_blank");



}





function sdformat1(){



    var content 	= document.getElementById('keys').value;

    var account 	= document.getElementById('acc').value;

    var sku 	= document.getElementById('sku').value;

    var country 	= document.getElementById('country').value;



    location.href= 'orderindex.php?keys='+content+"&account="+account+"&sku="+sku+"&module=orders&action=<?php echo $_REQUEST['action'];?>&ostatus=<?php echo $ostatus;?>&country="+country;





}



function ukformat(){
    var bill=getSelectOrder();

    if(bill == ""){



        alert("请选择订单号");

        return false;



    }





    //window.open("packlist.php?ordersn="+bill,"_blank");

    window.open("allpacklist.php?ordersn="+bill,"_blank");









}



function allformat(){
    var bill=getSelectOrder();

    if(bill == ""){



        alert("请选择订单号");

        return false;



    }





    window.open("allpacklist.php?ordersn="+bill,"_blank");











}























function detail2(){
    var bill=getSelectOrder();

    if(bill == ""){



        alert("请选择订单号");

        return false;



    }



    var url	= "exceltodetail2.php?type=delivery&bill="+bill;

    window.open(url,"_blank");





}

function detail3(){
    var bill=getSelectOrder();

    if(bill == ""){



        alert("请选择订单号");

        return false;



    }



    var url	= "labeladdress.php?type=delivery&bill="+bill;

    window.open(url,"_blank");





}





function bulkedit(){
    var bill=getSelectOrder();



    if(bill == ""){



        alert("Please select Orders");

        return false;



    }

    window.open("orderbulkeditorderstatus.php?module=orders&ordersn="+bill,"_blank");



}



function shippingtolist(){
    var bill=getSelectOrder();

    if(bill == ""){



        //	alert("Please select Orders");

        //	return false;



    }

    window.open("allpacklist.php?module=orders&ordersn="+bill,"_blank");



}

function mixingPrint(type){
    var bill=getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }

    var url="t.php?s=/Transport/Print/PrintAllCarrier&bill="+bill;

    if(type=='printAll'||type=='printAlldebug'){
        url+="&mod=1";
    }else{
        url+="&mod=2";
    }

    if(type=='printAlldebug'||type=='printAlldebug2'){
        url+="&debug=1";
    }
    window.open(url,"_blank");
}

function ordermarkprint(type){
    var bill=getSelectOrder();

    if(bill == ""){

        alert("请选择订单号");
        return false;

    }










    if(confirm('确认操作吗')){



        openwindow("orderprint.php?ordersn="+bill+"&type="+type,'',550,385);


    }



}


function ebaymarket02(){
    var bill=getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }



    if(confirm('您确认将选中订单进行发信吗?')){



        openwindow("ordermarket02.php?ordersn="+bill,'',550,385);
    }








}

function deleteById() {
    var bill=getSelectOrder();
    if(bill == "") {
        alert("请选择你要删除的订单！");
    } else if(confirm("您确定要删除选中的订单吗？")) {
        location.href = 'orderindex.php?type=d&id='+bill;
    }
}



//设定打开窗口并居中
function openwindow(url,name,iWidth,iHeight)
{
    var url; //转向网页的地址;
    var name; //网页名称，可为空;
    var iWidth; //弹出窗口的宽度;
    var iHeight; //弹出窗口的高度;
    var iTop = (window.screen.availHeight-30-iHeight)/2; //获得窗口的垂直位置;
    var iLeft = (window.screen.availWidth-10-iWidth)/2; //获得窗口的水平位置;
    window.open(url,name,'height='+iHeight+',,innerHeight='+iHeight+',width='+iWidth+',innerWidth='+iWidth+',top='+iTop+',left='+iLeft+',toolbar=no,menubar=no,scrollbars=yes,resizeable=no,location=no,status=no');
}

function labelto01(){
    var bill=getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }
    window.open("labelto01.php?ordersn="+bill,"_blank");
}


function labelto02(){
    var bill=getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }
    window.open("labelto02.php?ordersn="+bill,"_blank");
}

function labelto03(){
    var bill=getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }
    alert("提示：\n\r选定BCD列，在点击鼠标右键，选择设置单元格格式-》选择对齐=》选择自动换行=》选择确定，就可看到您想要的格式。")
    window.open("labelto02.php?ordersn="+bill,"_blank");
}


function labelto04(){
    var bill=getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }
    //alert("提示：\n\r选定BCD列，在点击鼠标右键，选择设置单元格格式-》选择对齐=》选择自动换行=》选择确定，就可看到您想要的格式。")
    window.open("label04.php?ordersn="+bill,"_blank");
}

function moveTo1722(){
    //var Shipping 	= document.getElementById('Shipping').value;
    var bill=getSelectOrder();
    if(bill == ""){
        alert("请选择订单");return;
    }

    window.open("moveto1722.php?ordersn="+bill,"_blank");
}

function move1738To1722(){
    //var Shipping 	= document.getElementById('Shipping').value;
    var bill=getSelectOrder();
    if(bill == ""){
        alert("请选择订单");return;
    }

    window.open("move1738to1722.php?ordersn="+bill,"_blank");
}

//拆分订单
function split_order(){
    //var Shipping    = document.getElementById('Shipping').value;
    var bill	= getSelectOrder();
    var j=bill.split(',').length;
    if(j > 1){
        alert("拆分订单只允许单个拆分！");
        console.log(j);
        return false;
    }
    if(bill == ""){
        alert("请选择订单");
        return false;
    }
    window.open("t.php?s=/Order/SplitOrder/index/ebayid/"+bill,"_blank");
}

//还原已拆分订单
function restore_order(){
    //var Shipping = document.getElementById('Shipping').value;
    var bill = getSelectOrder();
    var j = bill.split(',').length;
    if(j > 1){
        alert("请选择父订单，或者只选择其中一个子订单！");
        console.log(j);
        return false;
    }
    if(bill == ""){
        alert("请选择订单");
        return false;
    }
    var url = "t.php?s=/Order/SplitOrder/showRestoreDetail/ebayid/"+bill;
    layer.open({
        type: 2,
        title: '还原已拆分订单',
        shadeClose: true,
        shade: 0.2,
        area: ['550px', '400px'],
        content: url
    });
    return true;
}

function pimod(type){
    //var Shipping 	= document.getElementById('Shipping').value;
    var Shipping = '';
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("如果您不选择订单，将会打印当前分类下，所选择条件的所有订单");

    }
    if(type){
        if(!confirm("去除标记一般是要重新上传,或者上传失败，您确定要去除标记么?")){
            return false;
        }
        var url = "toxls/cancelmarket.php?bill="+bill+"&module=orders";window.open(url,"_blank");
    }else{
        var url	= "mod.php?bill="+bill+"&module=orders&ostatus=<?php echo $ostatus;?>&Shipping="+Shipping;
        window.open(url,"_blank");
    }


}

function distribute(){

    var url		= "screenorder.php?module=orders";
    openwindow(url,'',550,385);
}




function distribute03(){

    var bill    = getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }

    if(confirm('如果选择指定订单，将会对所选择的订单，进行配货, 如果是，请选择确认，如果不是请选择取消')){
        var url		= "screenorder03.php?module=orders&bill="+bill;
        //openwindow(url,'',550,385);

    }else{
        var url		= "screenorder03.php?module=orders";
        return false;
        //openwindow(url,'',550,385);
    }
    openwindow(url,'',800,500);
}

function eubtracknumber(){
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }

    var url		= "eubtracknumber.php?bill="+bill;
    openwindow(url,'',550,385);




}

function eubreapia4(){
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }

    alert("一次最多只能选择10个订单进行批量打印");

    var url		= "eubtracknumbera4.php?bill="+bill;
    openwindow(url,'',550,385);




}

function euba4pl(){
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }


    var url		= "eubtracknumbera420.php?bill="+bill;
    window.open(url,'_blank');





}
function euba42(){
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }

    //	alert("一次最多只能选择10个订单进行批量打印");

    var url		= "eubtracknumbera42.php?bill="+bill;
    window.open(url);




}



function eubtracknumberv3rm(pagesize){
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }
    //	alert("一次最多只能选择10个订单进行批量打印");
    var url		= "eubtracknumberrm.php?bill="+bill+"&pagesize="+pagesize;
    openwindow(url,'',550,385);
}

function eubjy(){
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }

    var url		= "eubtracknumberjy.php?bill="+bill;
    openwindow(url,'',550,385);




}


function eubreapi(){
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }

    var url		= "eubtracknumberrm.php?bill="+bill;
    openwindow(url,'',550,385);




}

function eubre(){
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }

    var url		= "eubtracknumberre.php?bill="+bill;
    openwindow(url,'',550,385);




}

function disifan00(){
    var bill	= getSelectOrder();


    if(bill == ""){

        alert("请选择订单号");
        return false;

    }

    window.open("labeltodsf11.php?module=orders&ordersn="+bill,"_blank");

}
function copyorders(bill){



    window.open("copyorders.php?ordersn="+bill);
}



function comorder(){


    if(confirm('您确认将当前订单分类下面的所有相同地址，相同地址的订单进行合并吗')){
        var url		= "comorder.php?ostatus=<?php echo $ostatus;?>";
        window.open(url,"_blank");
    }





}

function rta(ebayid,pid,type){
    var type=type||'';
    if(type==''){
        var url		= 'rma.php?ebayid='+ebayid+"&pid="+pid+"&add=1";
    }else{
        var url		= 'rmath.php?ebayid='+ebayid+"&pid="+pid+"&add=1";
    }
    openwindow(url,'',800,685);
}





function euba4batch(){

    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }


    var url		= "a4eubconfirm.php?bill="+bill;
    window.open(url);





}
function labeltoa4(){
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }


    var url		= "a4confirm.php?bill="+bill;
    window.open(url);





}


function displayselect(){

    var b	= 0;

    var bill	= getSelectOrder();

    var b=bill.split(',').length;
    document.getElementById('rows2').innerHTML="您已经选择 <font color=red>"+b+"</font> 条记录 ^_^";

    document.getElementById("filesidselect").selected=true;
    document.getElementById("orderoperationselect").selected=true;
    document.getElementById("printidselect").selected=true;



}


function exportstofiles(){

    var typevalue 	= document.getElementById('filesid').value;
    if(typevalue=='1304233'){
        window.open("toxls/labelto1304233.php?module=orders&ostatus=<?php echo $_GET['ostatus'];?>",'_blank');
        return;
    }

    var bill	= getSelectOrder();
    if(typevalue == '') return false;
    if(typevalue == 'getordercost'){
        url = 'getordercost.php';
        window.location = url;return false;
    }
    if(bill == ""&&typevalue!='fourdays'){
        alert("请先勾选要处理的订单！");
        return false;

/*        if(!confirm("如果您不选择任何订单，则导出当前分类下的所有订单")){
            return;
        }*/
    }
    var url='';
    switch (typevalue){
        case 'ku':url= "toxls/labeltokuxitong.php?module=orders&ordersn="+bill;
            alert('请注意红色标记的行表示已经导出过的,导入酷系统前请删掉!');
            break;
        case '0111':url		= "toxls/labelto0111.php?module=orders&ordersn="+bill;break;

        case '0117':url		= "labelto0117.php?module=orders&ordersn="+bill;break;

        case '0122':url		= "labelto0122.php?module=orders&ordersn="+bill;break;

        case '130226':url		= "labelto130226.php?module=orders&ordersn="+bill;break;

        case '1302261':url		= "labelto1302261.php?module=orders&type=1&ordersn="+bill;break;

        case '1302263':url		= "labelto1302261.php?module=orders&type=3&ordersn="+bill;break;


        case '1302266':url		= "labelto1302266.php?module=orders&type=3&ordersn="+bill;break;


        case '130407':url		= "labelto130407.php?module=orders&ordersn="+bill;break;

        case '1302264':url		= "labelto1302264.php?module=orders&type=3&ordersn="+bill;break;

        case '1302265':url		= "labelto1302265.php?module=orders&type=3&ordersn="+bill;break;

        case '1304071':url		= "labelto1304071.php?module=orders&ordersn="+bill;
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');break;


        case '1304072':url		= "labelto1304072.php?module=orders&ordersn="+bill;break;



        case '1302262':url		= "labelto1302262.php?module=orders&ordersn="+bill;
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');break;

        case '1302263':url		= "labelto1302263.php?module=orders&ordersn="+bill;break;



        case '105':url		= "labelto1105.php?module=orders&ordersn="+bill;break;

        case '1051':url		= "labelto11051.php?module=orders&ordersn="+bill;break;

        case '0131':url		= "labelto0131.php?module=orders&ordersn="+bill;break;

        case '01251':url		= "labelto01251.php?module=orders&ordersn="+bill;break;

        case '01252':url		= "labelto01252.php?module=orders&ordersn="+bill;break;

        case '103':url		= "labelto1013.php?module=orders&ordersn="+bill;break;


        case '1':url		= "labelto1001.php?module=orders&ordersn="+bill;break;

        case '01':url		 = "labelto1111.php?module=orders&ordersn="+bill;break;
        case 'fourdays':url	 = "toxls/fourdays_notshipped.php";break;

        case '订单导出仓库对照': url = "toxls/shopeeduizhaoexportxls.php?ordersn="+bill;break;
        case '订单导出LWE': url = "toxls/shopeelweexportxls.php?ordersn="+bill;break;
        case '订单导出预报模板': url = "toxls/shopeeyubaoexportxls.php?ordersn="+bill;break;

        case '001':url		= "labelto2222.php?module=orders&ordersn="+bill;break;



        case '2':url		= "labelto1002.php?module=orders&ordersn="+bill;break;


        case '3':url		= "labelto1003.php?module=orders&ordersn="+bill;break;


        case '4':url		= "labelto1004.php?module=orders&ordersn="+bill;break;

        case 'c1':url		= "labelto10c1.php?module=orders&ordersn="+bill;break;

        case 'c2':url		= "labelto10c2.php?module=orders&ordersn="+bill;break;

        case 'c3':url		= "labelto10c3.php?module=orders&ordersn="+bill;break;


        case 'c4':url		= "labelto10c4.php?module=orders&ordersn="+bill;break;



        case '44':url		= "labelto10044.php?module=orders&ordersn="+bill;break;

        case '20':url		= "labelto1020.php?module=orders&ordersn="+bill;break;

        case '5':url		= "labelto1005.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');break;


        case '0108':url		= "labelto0108.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";

            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');break;




        case '0109':url		= "toxls/labelto0108.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";

            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');break;


        case '0110':url		= "toxls/labelto0110.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";break;



        case '104':url		= "labelto1104.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";

            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');break;



        case '6':url		= "labelto1006.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";

            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');break;



        case '7':url		= "labelto1007.php?module=orders&ordersn="+bill;break;


        case '8':url		= "labelto1008.php?module=orders&ordersn="+bill;break;

        case '100':url		= "labelto1011.php?module=orders&ordersn="+bill;break;

        case '101':url		= "print/labelto1101.php?module=orders&ordersn="+bill;break;

        case '102':url		= "labelto1102.php?module=orders&ordersn="+bill;break;

        case '9':url		= "labelto1009.php?module=orders&ordersn="+bill;break;


        case '10':url		= "labelto1010.php?module=orders&ordersn="+bill;break;

        case '11':url		= "labelto1011.php?module=orders&ordersn="+bill;break;


        case '12':url		= "print/labelto1013y.php?module=orders&ordersn="+bill;break;

        case '108':url		= "labelto10107.php?module=orders&ordersn="+bill;break;

        case '111':url		= "labelto1019.php?module=orders&ordersn="+bill;break;



        case '112':url		= "toxls/labelto10112.php?module=orders&ordersn="+bill;break;



        case '15':
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
            url		= "labelto1015.php?module=orders&ordersn="+bill;break;


        case '106':
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
            url		= "labelto1017.php?module=orders&ordersn="+bill;break;

        case '130413':url		= "toxls/labelto130413.php?module=orders&ordersn="+bill;break;



        case '1304133':url		= "labelto13040728.php?module=orders&ordersn="+bill;break;


        case '113':
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
            url		= "labelto1113.php?module=orders&ordersn="+bill;break;

        case '1131':
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
            url		= "labelto11131.php?module=orders&ordersn="+bill;break;

        case '112601':
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
            url		= "labelto112601.php?module=orders&ordersn="+bill;break;

        case '112701':
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行\n条形码显示需要下载C39HrP24DhTt字体到控制面板中');
            url		= "labelto112701.php?module=orders&ordersn="+bill;break;

        case '110':
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
            url		= "labelto1018.php?module=orders&ordersn="+bill;break;

        case '116':url		= "labelto1106.php?module=orders&ordersn="+bill;break;

        case '92101':url		= "labelto92101.php?module=orders&ordersn="+bill;break;

        case '92102':url		= "labelto92102.php?module=orders&ordersn="+bill;break;

        case '92103':url		= "labelto92103.php?module=orders&ordersn="+bill;break;

        case '107':url		= "labelto1016.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";break;


        case '1329':url		= "printlabel1329.php?module=orders&ordersn="+bill;break;

        case '999':url		= "toxls/labelto1020csv.php?module=orders&ordersn="+bill;break;

        case '170509':
        	alert('文件导出后，您需要全选，右键 - 设置单格格式 - 对齐 - 选择自动换行');
        	url		= "toxls/labelto170509.php?module=orders&ordersn="+bill;break;
    }
    /*
        if(typevalue	== '0111'){
            var url		= "toxls/labelto0111.php?module=orders&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '0117'){
            var url		= "labelto0117.php?module=orders&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '0122'){
            var url		= "labelto0122.php?module=orders&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '130226'){
            var url		= "labelto130226.php?module=orders&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '1302261'){
            var url		= "labelto1302261.php?module=orders&type=1&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '1302263'){
            var url		= "labelto1302261.php?module=orders&type=3&ordersn="+bill;  // 发货清单
        }

        if(typevalue	== '1302266'){
            var url		= "labelto1302266.php?module=orders&type=3&ordersn="+bill;  // 发货清单
        }

        if(typevalue	== '130407'){
            var url		= "labelto130407.php?module=orders&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '1302264'){
            var url		= "labelto1302264.php?module=orders&type=3&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '1302265'){
            var url		= "labelto1302265.php?module=orders&type=3&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '1304071'){
            var url		= "labelto1304071.php?module=orders&ordersn="+bill;  // 发货清单
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
        }

        if(typevalue	== '1304072'){
            var url		= "labelto1304072.php?module=orders&ordersn="+bill;  // 发货清单
        }


        if(typevalue	== '1302262'){
            var url		= "labelto1302262.php?module=orders&ordersn="+bill;  // 发货清单
            alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
        }
        if(typevalue	== '1302263'){
            var url		= "labelto1302263.php?module=orders&ordersn="+bill;  // 发货清单
        }


        if(typevalue	== '105'){
            var url		= "labelto1105.php?module=orders&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '1051'){
            var url		= "labelto11051.php?module=orders&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '0131'){
            var url		= "labelto0131.php?module=orders&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '01251'){
            var url		= "labelto01251.php?module=orders&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '01252'){
            var url		= "labelto01252.php?module=orders&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '103'){
            var url		= "labelto1013.php?module=orders&ordersn="+bill;  // 发货清单
        }

        if(typevalue	== '1'){
            var url		= "labelto1001.php?module=orders&ordersn="+bill;  // 发货清单
        }
        if(typevalue	== '01'){
            var url		= "labelto1111.php?module=orders&ordersn="+bill;  // 发货清单
        }


        if(typevalue	== '001'){
            var url		= "labelto2222.php?module=orders&ordersn="+bill;  // 发货清单
        }


        if(typevalue	== '2'){
            var url		= "labelto1002.php?module=orders&ordersn="+bill;  // 发货清单
        }

        if(typevalue	== '3'){
            var url		= "labelto1003.php?module=orders&ordersn="+bill;  // 三态导出格式
        }

        if(typevalue	== '4'){
            var url		= "labelto1004.php?module=orders&ordersn="+bill;  // ebay csv 导出
        }
        if(typevalue	== 'c1'){
            var url		= "labelto10c1.php?module=orders&ordersn="+bill;  // ebay csv 导出
        }
        if(typevalue	== 'c2'){
            var url		= "labelto10c2.php?module=orders&ordersn="+bill;  // ebay csv 导出
        }
        if(typevalue	== 'c3'){
            var url		= "labelto10c3.php?module=orders&ordersn="+bill;  // ebay csv 导出
        }

        if(typevalue	== 'c4'){
            var url		= "labelto10c4.php?module=orders&ordersn="+bill;  // ebay csv 导出
        }


        if(typevalue	== '44'){
            var url		= "labelto10044.php?module=orders&ordersn="+bill;  // ebay csv 导出
        }
        if(typevalue	== '20'){
            var url		= "labelto1020.php?module=orders&ordersn="+bill;  // SalesHistory csv 导出
        }
        if(typevalue	== '5'){
            var url		= "labelto1005.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // ebay csv 导出

        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');

    }
    if(typevalue	== '0108'){
        var url		= "labelto0108.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // ebay csv 导出

        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');

    }


    if(typevalue	== '0109'){
        var url		= "toxls/labelto0108.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // ebay csv 导出

        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');

    }
    if(typevalue	== '0110'){
        var url		= "toxls/labelto0110.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // ebay csv 导出

    }

    if(typevalue	== '104'){
        var url		= "labelto1104.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // ebay csv 导出

        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');

    }

    if(typevalue	== '6'){
        var url		= "labelto1006.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";

        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');

    }

    if(typevalue	== '7'){
        var url		= "labelto1007.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }

    if(typevalue	== '8'){
        var url		= "labelto1008.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '100'){
        var url		= "labelto1011.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '101'){
        var url		= "print/labelto1101.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '102'){
        var url		= "labelto1102.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '9'){
        var url		= "labelto1009.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }

    if(typevalue	== '10'){
        var url		= "labelto1010.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '11'){
        var url		= "labelto1011.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }

    if(typevalue	== '12'){
        var url		= "print/labelto1013y.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '108'){
        var url		= "labelto10107.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '111'){
        var url		= "labelto1019.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }


    if(typevalue	== '112'){
        var url		= "toxls/labelto10112.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }


    if(typevalue	== '15'){
        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
        var url		= "labelto1015.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }

    if(typevalue	== '106'){
        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
        var url		= "labelto1017.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '130413'){
        var url		= "toxls/labelto130413.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }


    if(typevalue	== '1304133'){
        var url		= "labelto13040728.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }

    if(typevalue	== '113'){
        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
        var url		= "labelto1113.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '1131'){
        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
        var url		= "labelto11131.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '112601'){
        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
        var url		= "labelto112601.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '112701'){
        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行\n条形码显示需要下载C39HrP24DhTt字体到控制面板中');
        var url		= "labelto112701.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '110'){
        alert('文件导出后，您需要全先，右键 - 设置单格格式 - 对齐 - 选择自动换行');
        var url		= "labelto1018.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '116'){
        var url		= "labelto1106.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '92101'){
        var url		= "labelto92101.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '92102'){
        var url		= "labelto92102.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '92103'){
        var url		= "labelto92103.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '107'){
        var url		= "labelto1016.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // ebay csv 导出
    }

    if(typevalue	== '1329'){
        var url		= "printlabel1329.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }
    if(typevalue	== '999'){
        var url		= "toxls/labelto1020csv.php?module=orders&ordersn="+bill;  // SalesHistory csv 导出
    }

*/

    window.open(url,'_blank');
}

function printtofiles(){

    var typevalue 	= document.getElementById('printid').value;
    //var Shipping    = document.getElementById('Shipping').value;
    var Shipping 	= '';
    if(typevalue == '') return false;

    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单！");
        return false;
    }

    if(typevalue == 'printAmzOrder'){
      var url		= "toxls/printlabelamzorder.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== 'FedExInvoice'){
        var url		= "toxls/printlabelFedExInvoice.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== 'picksku'){
        alert('请务必保证本次打印的订单是同一运输方式');
        var url		= "toxls/printlabelPicksku.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== 'mixprint'){
        var url		= "cprint/oneskuprint.php?module=orders";  // 发货清单
    }
    if(typevalue	== '247'){
        var url		= "printlabel247.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }

    if(typevalue	== '248'){
        var url		= "printlabel248.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }

    if(typevalue	== '201'){
        var url		= "printlabel201.php?module=orders&bill="+bill;  // 发货清单
    }



    if(typevalue	== '249'){
        var url		= "printlabel249.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }

    if(typevalue	== '18'){
        var url		= "printlabel1018.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== '181'){
        var url		= "printlabel10181.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== '1811'){
        var url		= "printlabel10181_ordinary.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== '1812'){
        var url		= "printlabel101812.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== '244'){
        var url		= "printlabel1018244.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }


    if(typevalue	== '1'){
        var url		= "printlabel1001.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== '121001'){
        var url		= "printlabel121001.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== '2'){
        var url		= "printlabel1002.php?module=orders&ordersn="+bill;  // 国际EUB A4打印
    }

    if(typevalue	== '3'){
        var url		= "printlabel1003.php?module=orders&ordersn="+bill;  // 国际EUB热敏打印
    }

    if(typevalue	== '4'){
        var url		= "printlabel1004.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== '241'){
        var url		= "printlabel10024.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }

    if(typevalue	== '305'){
        var url		= "printlabel10305.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }

    if(typevalue	== '40'){
        var url		= "printlabel1040.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }


    if(typevalue	== '41'){
        var url		= "printlabel1041.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }

    if(typevalue	== '42'){
        var url		= "printlabel1042.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }

    if(typevalue	== '182'){
        var url		= "printlabel1182.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }

    if(typevalue	== '5'){
        var url		= "printlabel1005.php?module=orders&ordersn="+bill;  // 国际eub发货清单
    }


    if(typevalue	== '6'){
        var url		= "printlabel1006.php?module=orders&ordersn="+bill;  // 国际eub发货清单
    }

    if(typevalue	== '7'){
        var url		= "printlabel1007.php?module=orders&ordersn="+bill;  // 地址打印每页10个
    }

    if(typevalue	== '8'){
        var url		= "printlabel1008.php?module=orders&ordersn="+bill;  // 一页8个，带sku和条码
    }

    if(typevalue	== '9'){
        var url		= "printlabel1009.php?module=orders&ordersn="+bill;  // 一页8个，带sku和条码
    }

    if(typevalue	== '10'){
        var url		= "printlabel1010.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }


    if(typevalue	== '245'){
        var url		= "printlabel10245.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }
    if(typevalue	== '246'){
        var url		= "labelto3002.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }
    if(typevalue	== '11'){
        var url		= "printlabel1011.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }

    if(typevalue	== '12'){
        var url		= "printlabel1012.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }


    if(typevalue	== '13'){
        var url		= "printlabel1013.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }


    if(typevalue	== '14'){
        var url		= "printlabel1014.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }
    if(typevalue	== '15'){
        var url		= "printlabel1015.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }

    if(typevalue	== '141'){
        var url		= "printlabel10141.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }
    if(typevalue	== '151'){
        var url		= "printlabel10151.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }
    if(typevalue	== '16'){
        var url		= "packslipnew.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }
    if(typevalue	== '13331'){
        var url		= "printlabel1333.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }
    if(typevalue	== '13332'){
        var url		= "printlabel13331.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }
    if(typevalue	== '130413'){
        var url		= "toxls/printlabel130413.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }

    if(typevalue	== '17'){
        var url		= "printlabel1017.php?module=orders&ordersn="+bill;  // 拣货清单+条码
    }

    if(typevalue	== '20'){
        alert('请设置打印格式  左右边距8 上下5');
        var url		= "printlabel1020.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // 拣货清单+条码
    }
    if(typevalue	== '19'){
        var url		= "printlabel1019.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // 拣货清单+条码
    }
    if(typevalue	== '0314'){
        var url		= "printlabel130314.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // 拣货清单+条码
    }
    if(typevalue	== '03141'){
        var url		= "printlabel1303141.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // 拣货清单+条码
    }
    if(typevalue	== '0116'){
        var url		= "printlable0116.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // 拣货清单+条码
    }
    if(typevalue	== '21'){
        var url		= "printlabel1030.php?module=orders&ordersn="+bill+"&ostatus=<?php echo $ostatus;?>";  // 拣货清单+条码
    }
    if(typevalue	== '22'){
        var url		= "printlabel1022.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== '222'){
        var url		= "printlabel10222.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== '23'){
        var url		= "printlabel1023.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单
    }
    if(typevalue	== '24'){
        var url		= "curltest.php?ebay_id="+bill+"type=print_a4";  // 燕文a4
    }
    if(typevalue	== '25'){
        var url		= "curltest.php?ebay_id="+bill+"type=print_a5";  // 燕文a5
    }
    if(typevalue	== '26'){
        var url		= "curltest.php?ebay_id="+bill+"type=print_a6";  // 燕文a6
    }
    if(typevalue	== '27'){
        var url		= "printlabel1033.php?ebay_id="+bill;  // 燕文a6
    }


    if(typevalue	== '183'){
        var url		= "printlabel1183.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单


    }


    if(typevalue	== '1833'){
        var url		= "printlabel11833.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus=<?php echo $ostatus;?>";  // 发货清单


    }

    if(typevalue	== '130414'){
        var url		= "toxls/labelto130414.php?module=orders&ordersn="+bill;  // ebay csv 导出
    }




    window.open(url,'_blank');




}

function bookhackorer(){
    var count	= 0;
    var bill	= getSelectOrder();


    if(bill.split(',').length>=2){
        alert("一次只能操作一个订单");
        return false;
    }
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }

    openwindow("bookbackorer.php?ordersn="+bill,'',550,385);
}


function addoutorder(){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单");
        return false;
    }
    var url	= "battchtoaddoutorder.php?bill="+bill;
    if(confirm("您确认将这些订单标记出库吗")){
        window.open(url,"_blank");
    }


}



function auditorder(){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单");
        return false;
    }
    var url	= "battchtoauditorder.php?bill="+bill;
    if(confirm("您确认将这些订单标记出库吗")){
        window.open(url,"_blank");
    }


}
function	applycode(){

    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }

    var url="addcode.php?module=orders&bill="+bill;
    openwindow(url,'',550,385);
}
function posttoyanwen(){
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }


    var url		= "curltest.php?ebay_id="+bill+"&type=create";
    openwindow(url,'',300,200);
}

function eubtracknumberv3(){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    var url		= "eubtracknumberv3.php?bill="+bill;
    openwindow(url,'',550,385);
}

function eubtracknumberv4(type){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    var url='';
    if('10_v4'==type){// 上传并 交运
        url="eub/eubtracknumberv2.php?bill="+bill;
    }
    if('11_v4'==type){// 交运
        url="eub/eubtracknumberjyv2.php?bill="+bill;
    }
    if('12_v4'==type){// 热敏
        url="eub/eubtracknumberrmv2.php?bill="+bill+"&pagesize=1";
        var url1 = "toscan.php?bill="+bill;
    }
    if('13_v4'==type){// A4
        url="eub/eubtracknumberrmv2.php?bill="+bill+"&pagesize=0";
    }
    if('14_v4'==type){//取消
        url="eub/canceleubtracknumberv2.php?bill="+bill;
    }
    if('15_v4'==type){//补发
        url="eub/eubtracknumberrev2.php?bill="+bill;
    }

    if('16_v4'==type){//自制面单
        url="eubprint/eubindex.php?bill="+bill;
        window.open(url,'_blank');return;
    }
    if(url==''){
        alert("选择无效!");
        return;
    }
    if(url1){
      window.open(url1,'_blank');
    }
    openwindow(url,'',550,385);
    return;
}

function hkeub(){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    var url		= "hkeub.php?bill="+bill;
    openwindow(url,'',550,385);
}


function pxsubmit(){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    var url		= "orderonline_test.php?type=creat&bill="+bill;
    openwindow(url,'',550,385);
}
function exportdelivery(){

    var start		= document.getElementById('start').value;
    var end			= document.getElementById('end').value;

    var bill	= getSelectOrder();
    if(bill == ""){

        alert("如果不选择订单则，则导出当前分类下的所有订单");
        //return false;
    }
    var url		= "exporttodelivery.php?bill="+bill+"&ostatus=<?php echo $ostatus;?>&start="+start+"&end="+end;
    openwindow(url,'',550,385);
}
function salereport(){
    var start = document.getElementById("start").value;
    var end = document.getElementById("end").value;
    var acc = document.getElementById("acc").value;
    var url  = "salesreporttoxls.php?ostatus=<?php echo $ostatus;?>&start="+start+"&end="+end+"&acc="+acc;
    window.open(url,"_blank");
}

function eubv3jy(){

    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    var url		= "eubtracknumberjyv3.php?bill="+bill;
    openwindow(url,'',550,385);
}

function canceleubtracknumber(){

    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    var url		= "canceleubtracknumber.php?bill="+bill;
    openwindow(url,'',550,385);



}

function savesku(id){
    var skuvalue		= document.getElementById('sku'+id).value;
    var url				= "getajax2.php";
    var param			= "type=changesku&id="+id+"&sku="+skuvalue;
    //mstatusid			=  id;
    alert("该功能已经被禁用!");
    // sendRequestPost(url,param);


}




function sendRequestPost(url,param){
    createXMLHttpRequest();
    xmlHttpRequest.open("POST",url,true);
    xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    xmlHttpRequest.onreadystatechange = processResponse;
    xmlHttpRequest.send(param);
}
//处理返回信息函数
function processResponse(){
    if(xmlHttpRequest.readyState == 4){
        if(xmlHttpRequest.status == 200){
            var res = xmlHttpRequest.responseText;
            if(res == '0'){
                document.getElementById('mstatus'+mstatusid).innerHTML="<font color=Green>Success</font>";
                document.getElementById('sku'+mstatusid).focus();
            }else{
                document.getElementById('mstatus'+mstatusid).innerHTML="<font color=red>Failure</font>";
            }

            //	document.getElementById('content'+msid).value = res;
        }
        //
    }

}


var xmlHttpRequest;
function createXMLHttpRequest(){
    try
    {
        xmlHttpRequest=new XMLHttpRequest();
    }
    catch (e)
    {
        try
        {
            xmlHttpRequest=new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e)
        {
            try
            {
                xmlHttpRequest=new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e)
            {
                alert("您的浏览器不支持AJAX！");
                return false;
            }

        }

    }
}

/*
document.onkeydown=function(event){
    e = event ? event :(window.event ? window.event : null);
    if(e.keyCode==13){
        searchorder();
    }
}*/

function creatordereubxx(type){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    if(type == 0) {
      var url = "eubxx.php?bill=" + bill + "&type=creat";
    }
    if(type == 1){
      var url = "neweub.php?bill=" + bill + "&type=creat";
    }
    openwindow(url,'',550,385);
}
function deleubxx(type){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    if(type == 0) {
      var url = "eubxx.php?bill=," + bill + "&type=del";
    }
    if(type == 1){
      var url = "neweub.php?bill=," + bill + "&type=del";
    }
    openwindow(url,'',550,385);
}
function printeubxx(type){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    if(type == 0){
      var url		= "eubxx.php?bill="+bill+"&type=print";
    }
    if(type == 1){
      var url		= "neweub.php?bill="+bill+"&type=print";
    }
    var url1  = "toscan.php?bill="+bill;
    window.open(url1,'_blank');
    window.open(url,'_blank');
}

function eubtracknumberv3rmHK(pagesize){
    var bill	= getSelectOrder();
    if(bill == ""){

        alert("请选择订单号");
        return false;

    }
    //	alert("一次最多只能选择10个订单进行批量打印");
    var url		= "eubtracknumberrmHK.php?bill="+bill+"&pagesize="+pagesize;
    openwindow(url,'',550,385);
}
function submit4px(){
    var bill	= getSelectOrder();
    if(bill==''){
        alert('请选择订单');
        return false;
    }
    if(confirm('确定上传并确认吗')){

        window.open("test4px.php?ebay_id="+bill,"_blank");

    }
}
function submit4pxstatus(){
    var bill	= getSelectOrder();
    if(bill==''){
        alert('请选择订单');
        return false;
    }
    window.open("status4px.php?type=status&ebay_id="+bill,"_blank");
}
function check4pxfee(){
    var bill	= getSelectOrder();
    window.open("status4px.php?type=orderfee&id="+bill,"_blank");
}

function submitcky(){
    var bill	= getSelectOrder();
    if(bill==''){
        alert('请选择订单');
        return false;
    }
    if(confirm('确定上传并确认吗')){

        window.open("OutStoreAddOrder.php?ebay_id="+bill,"_blank");

    }
}
function tracknumbercky(){
    var bill	= getSelectOrder();
    if(bill==''){
        alert('请选择订单');
        return false;
    }

    window.open("OutStoreAddOrder1.php?ebay_id="+bill,"_blank");

}
function submitm2c(){
    var bill	= getSelectOrder();
    if(bill==''){
        alert('请选择订单');
        return false;
    }
    if(confirm('确定上传并确认吗')){

        window.open("M2cAddorder.php?ebay_id="+bill,"_blank");

    }
}
function tracknumberm2c(){
    var bill	= getSelectOrder();
    if(bill==''){
        alert('请选择订单');
        return false;
    }

    window.open("M2cAddordr2.php?ebay_id="+bill,"_blank");

}

function submitzx(){
    var bill	= getSelectOrder();
    if(bill==''){
        alert('请选择订单');
        return false;
    }
    if(confirm('确定上传并确认吗')){

        window.open("ExpressAddorderNew.php?ebay_id="+bill,"_blank");

    }
}
function tracknumberzx(){
    var bill	= getSelectOrder();
    if(bill==''){
        alert('请选择订单');
        return false;
    }

    window.open("ExpressGetPackage.php?ebay_id="+bill,"_blank");

}



function orderoperation(that){

    var typevalue 	= that.value;
    var bill = getSelectOrder();

    if(typevalue == 'printAll'||typevalue == 'printAll2'||typevalue == 'printAlldebug'||typevalue == 'printAlldebug2'){
        mixingPrint(typevalue);
    }
    if(typevalue == '0') ordermarkprint('add');
    if(typevalue == '1') ordermarkprint('del');
    if(typevalue == '2') combine();
    if(typevalue == '3') comorder();
    if(typevalue == '4') location.href='ordermodifive.php?module=orders&type=addorder&action=Add new Order';//创建订单 旧
    if(typevalue == '41') { createOrder(); return false; }     //创建订单 新
    if(typevalue == '5') location.href='ordersumaitongupload.php?module=orders&amp;type=addorder&amp;action=速买通导入模板';
    if(typevalue == '6') location.href='orderukupload.php?module=orders&amp;type=addorder&amp;action=Amazon';
    if(typevalue == '7') location.href='orderuploadloader.php?module=orders&amp;type=addorder&amp;action=Amazon';
    if(typevalue == '8') location.href='salehistoryupload.php?module=orders&amp;type=addorder&amp;action=ebay csv导入';
    
    if(typevalue == 'LWE导入ERP') location.href="orderuploadtolwe.php?ordersn=";
    //if(typevalue == '36') location.href='orderupload2.php?module=orders&amp;type=addorder&amp;action=自定义模板 csv导入';
    if(typevalue == '37') location.href='orderupload3.php?module=orders&amp;type=addorder&amp;action=测试1 csv导入';
    if(typevalue == 'xsfh') xiaobaoGuahao('xsfh');
    if(typevalue == 'xstrack') xiaobaoGuahao('xstrack');
    if(typevalue == 'xsprint') xiaobaoGuahao('xsprint');
    if(typevalue == 'znjh') location.href='smartpick.php?module=orders&amp;type=addorder&amp;action=智能捡货';
    if(typevalue == '9re'){
        reMatchRule();
        return;
    }
    if(typevalue == 'fail_analysis'){var url='orderSiptype.php?type=orderSiptype&action=运输方式分派失败分析'; window.open(url,'_blank');}
    if(typevalue == '9de') pimod(1);
    if(typevalue == '9') pimod(0);
    if(typevalue == '10') eubtracknumberv3();
    if(typevalue == '11') eubv3jy();
    if(typevalue == '12') eubtracknumberv3rm(1);
    if(typevalue == '13') eubtracknumberv3rm(0);
    if(typevalue == '14') canceleubtracknumber();
    if(typevalue == '15') eubre();
    if(typevalue == '16_v4'||typevalue == '15_v4'||typevalue=='14_v4'||typevalue=='13_v4'||typevalue=='12_v4'||typevalue=='11_v4'||typevalue=='10_v4'){
        eubtracknumberv4(typevalue);
    }

    if(bill == ''){
        layer.msg('请先选择订单！', {icon:2});
        return false;
    }
    
    if(typevalue == 'lazadaprint'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/LGS/batchOrderAction/ordersn/"+bill+"/type/print";
        window.open(url,'_blank');
    }
    if(typevalue == 'shopeeprint'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/YuanTong/batchOrderAction/ordersn/"+bill+"/type/print";
        window.open(url,'_blank');
    }
    if(typevalue == 'lweprint'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/LWE/batchOrderAction/ordersn/"+bill+"/type/print";
        window.open(url,'_blank');
    }
    if(typevalue == 'lweallocate'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Order/Shopee/allocateTracknumber/ordersn/"+bill+"/type/print";
        window.open(url,'_blank');
    }
    if(typevalue == 'kjyt'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/KuaJingETong/batchOrderAction/ordersn/"+bill+"/type/create";
        window.open(url,'_blank');
    }
    if(typevalue == 'kjytdy'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/KuaJingETong/batchOrderAction/ordersn/"+bill+"/type/print";
        window.open(url,'_blank');
    }
    if(typevalue == 'chukou1_create'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/ChuKou1/batchOrderAction/ordersn/"+bill+"/type/create";
        window.open(url,'_blank');
    }
    if(typevalue == 'chukou1_print'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/ChuKou1/batchOrderAction/ordersn/"+bill+"/type/print";
        window.open(url,'_blank');
    }
    if(typevalue == 'liniocreate'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/MailAmericas/batchOrder/ordersn/"+bill+"/type/create";
        window.open(url,'_blank');
    }
    if(typevalue == 'linioprint'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/MailAmericas/batchOrder/ordersn/"+bill+"/type/print";
        window.open(url,'_blank');
    }
    // if(typevalue == 'winit_create'){
    //     var bill=getSelectOrder();
    //     if(bill == ""){
    //         alert("请选择订单");
    //         return false;
    //     }
    //     var url = "/t.php?s=/Transport/Winit/batchOrder/ordersn/"+bill+"/type/create";
    //     window.open(url,'_blank');
    // }
    // if(typevalue == 'winit_print'){
    //     var bill=getSelectOrder();
    //     if(bill == ""){
    //         alert("请选择订单");
    //         return false;
    //     }
    //     var url = "/t.php?s=/Transport/Winit/batchOrder/ordersn/"+bill+"/type/print";
    //     window.open(url,'_blank');
    // }
    // if(typevalue == 'winit_endOrder'){
    //     var bill=getSelectOrder();
    //     if(bill == ""){
    //         alert("请选择订单");
    //         return false;
    //     }
    //     var url = "/t.php?s=/Transport/Winit/batchOrder/ordersn/"+bill+"/type/endOrder";
    //     window.open(url,'_blank');
    // }
    // if(typevalue == 'winit_sureOrder'){
    //     var bill=getSelectOrder();
    //     if(bill == ""){
    //         alert("请选择订单");
    //         return false;
    //     }
    //     var url = "/t.php?s=/Transport/Winit/batchOrder/ordersn/"+bill+"/type/sureToDelivery";
    //     window.open(url,'_blank');
    // }
    if(typevalue == 'dlty_create'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/DingLi/batchOrderAction/ordersn/"+bill+"/type/create";
        window.open(url,'_blank');
    }
    if(typevalue == 'dlty_print'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/DingLi/batchOrderAction/ordersn/"+bill+"/type/print";
        window.open(url,'_blank');
    }
    if(typevalue == 'dhl_create'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/DHL/batchOrderAction/ordersn/"+bill+"/type/create";
        window.open(url,'_blank');
    }
    if(typevalue == 'dhl_print'){
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单");
            return false;
        }
        var url = "/t.php?s=/Transport/DHL/batchOrderAction/ordersn/"+bill+"/type/print";
        window.open(url,'_blank');
    }

    if(typevalue=='moveto1722'){
        // 2016 11 7 必须从这里转到 等待分配
        moveTo1722();return;
    }
    if('1738moveto1722'==typevalue){
        move1738To1722();return;
    }
    //拆分订单
    if(typevalue == 'split_order'){
        split_order();
        return;
    }
    //还原已拆分订单
    if(typevalue == 'restore_order'){
        restore_order();
        return;
    }

    if(typevalue == 'cancel_order') cancel_order();

    if(typevalue == 'quick'){
        var bill=getSelectOrder();
        var num=bill.split(',').length;
        if(num > 2){
            alert('最多只允许选择2个订单！');return false;
        }
      if(bill == ""){
        alert("请选择订单");
        return false;
      }
      var url = "toxls/quicklystopdelivery.php?bill="+bill;
      window.open(url,"_blank");
    }
    if(typevalue == '16') creatordereubxx(0);
    if(typevalue == '17') deleubxx(0);
    if(typevalue == '18') printeubxx(0);
    if(typevalue == 'new16') creatordereubxx(1);
    if(typevalue == 'new17') deleubxx(1);
    if(typevalue == 'new18') printeubxx(1);
    if(typevalue == '19') hkeub();
    if(typevalue == '20') eubtracknumberv3rmHK(0);
    if(typevalue == '21') eubtracknumberv3rmHK(1);
    if(typevalue == '22') eubtracknumberv3rmHK(2);
    if(typevalue == '23') eubtracknumberv3rmHK(3);
    if(typevalue == '24') pxsubmit();
    if(typevalue == '25') submit4px();
    if(typevalue == '26') submit4pxstatus();
    if(typevalue == '27') check4pxfee();
    if(typevalue == '28') submitcky();
    if(typevalue == '29') tracknumbercky();
    if(typevalue == '30') submitm2c();
    if(typevalue == '31') tracknumberm2c();
    if(typevalue == '32') submitzx();
    if(typevalue == '33') tracknumberzx();
    if(typevalue == '34') stsubmit('creat');
    if(typevalue == '34print') stsubmit('print');
    if(typevalue == '38') dgsubmit('creat');
    if(typevalue == '38print') dgsubmit('print');
    if(typevalue == '38del') dgsubmit('del');
    if(typevalue == '35') pxsubmitv2();
    if(typevalue == '36') pxsubmitv2('print');
    if(typevalue == 'fgzx') ajsubmit('creat');
    if(typevalue == 'fgzxdy') ajsubmit('print');
    if(typevalue == 'jms') jmssubmit('creat');
    if(typevalue == 'jms1') jmssubmit('print');
    if(typevalue == 'ost') ostsubmit('creat');
    if(typevalue == 'ost1') ostsubmit('print');
    if(typevalue == 'ost2') ostsubmit('reGetLabel');
    if(typevalue == 'cdeub_submit' || typevalue=='cdeub_print') cdeub_submit(typevalue);
// 万易通 库系统  英国仓
    var HaiWaiCangArray=[
        '40','41','42','43','wyttj','wishtj','fzuporder',
        'fzprint','rduporder','rdprint','wishprint','wytjy',
        'wytdel','wytprint','cool_1','cool_2','cool_3',
        'uklh_1','uklh_2','uklh_3','uklh_4',
        'ykd_1','ykd_2','ykd_3','ykd_4',
        'syuporder','syprint','sydelete'
    ];
    var xiaoBaoGuaHaoArray=[
        '45',
        '46',
        '46A',
        '46P',
        '47',
        '47xj',
        '48',
        '49',
        'xp1',
        'xp2',
        'xp3',
        'est',
        'est1',
        'byb',
        'bybtn',
        'euruppy',
        'eurupgh',
        'jjxbpy',
        'jjxbgh',
        'eurdown',
        'rspy',
        'rsgh',
        'rsprintpy',
        'rsprintgh',
        'postgh',
        'postpy',
        'postprintgh',
        'postprintpy',
        'uploadtno',
        'ubixb',
        'ubixbpdf',
        'teauporder',
        'teaprint'
    ];
    if(SearchVal(typevalue,HaiWaiCangArray)>=0){
        wytapi(typevalue);
        return;
    }
    if(SearchVal(typevalue,xiaoBaoGuaHaoArray)>=0){
        xiaobaoGuahao(typevalue);
        return;
    }

    //派敏斯美国专线
    var bill = getSelectOrder();
    if(bill == ""){
        alert("请选择订单");
        return false;
    }
    if(typevalue == 'pms_select_store_code'){   //选择仓库代码
        var url = "/t.php?s=/Transport/Pms/selectStoreCode/ebay_id/" + bill;
        layer.open({
            type: 2,
            title: '选择仓库代码',
            shadeClose: true,
            shade: 0.2,
            area: ['550px', '400px'],
            content: url
        });
        return;
    }
    if(typevalue == 'pms_create'){  //上传订单，创建物流包裹
        if(!confirm("是否确定要将订单上传到pms物流平台？")){
            return false;
        }
        var url = "/t.php?s=/Transport/Pms/createPackages/ebay_id/"+bill;
        window.open(url, '_blank');
        return;
    }
    if(typevalue == 'pms_print'){
        var url = "/t.php?s=/Transport/Pms/printOrder/ebay_id/"+bill;
        window.open(url, '_blank');
        return;
    }
    if(typevalue == 'pms_delete'){  //删除上传订单、物流包裹
        if(!confirm("是否确定要从pms物流平台删除该订单订单？")){
            return false;
        }
        var url = "/t.php?s=/Transport/Pms/deletePackages/ebay_id/"+bill;
        window.open(url, '_blank');
        return;
    }

}

function cancel_order()
{
    var checkedBoxes = $('input[type="checkbox"]:checked');

    if (!checkedBoxes) {
        alert('请先勾选要处理的订单！');
        return 0;
    }

    var confirm_re = confirm("订单已出库 !\n是否确认退回 !\n退回之后订单将进入回收站！");
    if (confirm_re) {
        var orderIds = '';
        $.each(checkedBoxes , function(key , val) {
            orderIds = orderIds+','+$(val).val();
        });
        var url = './include/cancel_order.php?orderids='+orderIds;
        window.open(url , '_blank')
    } else {
        return 0;
    }

}


function UploadHaerbinTnum(){
    var url		= "include/Uploadhrbtracknumber.php";
    window.open(url,'_blank');
}

// 重新运行规则
function reMatchRule(){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单");
        return false;
    }
    var url="t.php?s=/Order/OrderRuleHandle&bill="+bill+"&debug=1";
    window.open(url,'_blank');
}

function xiaobaoGuahao(num){ // 文慧，燕文 小包挂号
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单");
        return false;
    }
    //if(num=='45'){var url		= "hrbtracknumbers.php?bill="+bill;}
    if(num=='45'){var url		= "wenhui/wenhuihandle.php?type=creat&bill="+bill;}
    //if(num=='46'){var url		= "hrbtracknumberprint.php?bill="+bill;}
    if(num=='46'){var url		= "wenhui/wenhuihandle.php?type=print&bill="+bill;}
    if(num=='46A'){
        if(confirm("警告:您正在操作取消文慧小包订单!您确认操作么?")){
            var url		= "wenhui/wenhuihandle.php?type=delete&bill="+bill;
        }else{
            var url=false;
        }
    }
    if(num=='46P'){var url		= "wenhui/wenhuihandle.php?type=printmyself&bill="+bill;var url1 = "toscan.php?bill="+bill;}
    if(num=='47'){var url		= "./yanwen/index.php?bill="+bill;}
    if(num=='47xj'){var url		= "./yanwen/index.php?bill="+bill+"&xj=1";}
    if(num=='48'){var url		= "./yanwen/print.php?lable=10&bill="+bill;}
    if(num=='49'){var url		= "./yanwen/print.php?lable=a4&bill="+bill;}
    if(num=='xp1'){var url		= "./xiapu/index.php?action=upload&bill="+bill;}
    if(num=='xp2'){var url		= "./xiapu/index.php?action=del&bill="+bill;}
    if(num=='xp3'){var url		= "./xiapu/print.php?bill="+bill;}
    if(num=='est1'){var url		= "./ruston/est_upload.php?bill="+bill;}
    if(num=='jms'){var url		= "./ruston/est_upload.php?bill="+bill;}
    if(num=='est'){var url		= "./ruston/est_print.php?bill="+bill;var url1 = "./toscan.php?bill="+bill;}
    if(num=='byb'){var url		= "./toxls/byb_applyorderv2.php?bill="+bill;}
    if(num=='bybtn'){var url		= "./toxls/byb_print.php?bill="+bill+"&ebay_carrier="+ebay_carrier;}
    //速卖通线上发货线上打印标签
    if(num=='xsprint'){var url		= "./toxls/smt_xs_print.php?bill="+bill;var url1 = "./toscan.php?bill="+bill;}
    //线上发货上传
    if(num=='xsfh'){var url		= "./smttracknumber.php?type=smt&bill="+bill;}
    if(num=='xstrack'){var url		= "./smttracknumber.php?type=track&bill="+bill;}
    if(num=='xsfhresend'){var url		= "./smttracknumber.php?type=smtresend&bill="+bill;}
    if(num=='xstoyuntu'){var url		= "./smttracknumber.php?type=toyuntu&bill="+bill;}
    if(num=='euruppy'){var url='./eurxiaobao/eurup.php?bill='+bill;}
    if(num=='eurupgh'){var url='./eurxiaobao/eurup.php?bill='+bill;}
    if(num=='eurdown'){var url='./eurxiaobao/eurdown.php?bill='+bill;var url1 = "./toscan.php?bill="+bill;}
    if(num=='rsgh'){var url='./rsxiaobao/rsindex.php?bill='+bill+'&type=10';}
    if(num=='rspy'){var url='./rsxiaobao/rsindex.php?bill='+bill+'&type=9';}
    if(num=='rsprintpy'){var url='./rsxiaobao/rsprint.php?bill='+bill+'&type=9';var url1 = "./toscan.php?bill="+bill;}
    if(num=='rsprintgh'){var url='./rsxiaobao/rsprint.php?bill='+bill+'&type=10';var url1 = "./toscan.php?bill="+bill;}
    if(num=='postgh')   {var url='./chinapost/index.php?bill='+bill+'&type=10';}
    if(num=='postpy')   {var url='./chinapost/index.php?bill='+bill+'&type=9';}
    if(num=='postprintgh'){var url='./chinapost/postghprint.php?bill='+bill+'&type=10';}
    if(num=='postprintpy'){var url='./chinapost/postprint.php?bill='+bill+'&type=9';}
    if(num=='uploadtno'){var url='./chinapost/uploadtno.php'}
    if(num=='ubixb'){var url='./etower/etowerindex.php?bill='+bill+'&type=create';}
    if(num=='ubixbpdf'){
        var url='./etower/etowerindex.php?bill='+bill+'&type=pdf';
    }
    if(num=='teauporder'){var url='./TransitEuropeAsia/uploadorder.php?bill='+bill+'&type=create';}
    if(num=='teaprint'){var url='./TransitEuropeAsia/uploadorder.php?bill='+bill+'&type=print';}

    if(url==false){return;}
    if(url1) {
      window.open(url1, '_blank');
    }
    window.open(url,'_blank');
}

// 万易通 库系统  英国仓
function wytapi(type){
    var bill=getSelectOrder();
    var j=bill.split(',').length;
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
//万易通 海外仓操作
    var typeArr={
        "40":"create",
        "41":"delete",
        "42":"track",
        "43":"outer"
    };
//万易通 小包子 操作
    var typeArrXb={
        "wytprint":"print",
        "wyttj":"create",
        "wytjy":"jiaoyun",
        "wytdel":"delete"
    };

    //易可达美东仓 操作
    var typeArrYKD={
        "ykd_1":"create",
        "ykd_2":"track",
        "ykd_3":"outer",
        "ykd_4":"delete"
    };

    //顺有操作 操作
    var typeArrSY={
        "syuporder":"create",
        "syprint":"print",
        "sydelete":"delete"
    };


    if(undefined!=typeArr[type]){
        var url		= "wyt/wytapi.php?type=creat&bill="+bill+"&type="+typeArr[type];
        window.open(url,'_blank');
    }

    if(undefined!=typeArrXb[type]){
        if('delete'==typeArrXb[type]){
            if(!confirm("警告!您即将作废选中的订单，您确操作么?")){
                return;
            }
        }
        if(typeArrXb[type] == 'print') {
          var url1 = "toscan.php?bill=" + bill;
          window.open(url1,'_blank');
        }
        var url = "wyt/wytispindex.php?type=creat&bill=" + bill + "&type=" + typeArrXb[type];
        window.open(url, '_blank');
        return;
    }

    if(type=='wishtj'){
        var url		= "wish/uporder.php?bill="+bill;
        window.open(url,'_blank');return;
    }

    if(type=='fzuporder'||type=='rduporder'){
        var url		= "yuntu/uporder.php?bill="+bill+"&method=uporder";
        window.open(url,'_blank');return;
    }

    if(type=='fzprint'||type=='rdprint'){
        var url		= "yuntu/uporder.php?bill="+bill+"&method=printorder";
        var url1 = "./toscan.php?bill="+bill;
        window.open(url1,'_blank');
        window.open(url,'_blank');return;
    }

    if(type=='wishprint'){
        var url		= "wish/wishprint.php?bill="+bill;
        window.open(url,'_blank');return;
    }

    if(type=='cool_1'){
        var url		= "coolxitong/uporder.php?bill="+bill;
        window.open(url,'_blank');return;
    }
    if(type=='cool_2'){
        var url		= "coolxitong/gettrackno.php?bill="+bill;
        window.open(url,'_blank');return;
    }
    if(type=='cool_3'){
        var url		= "coolxitong/uptoshipped.php?bill="+bill;
        window.open(url,'_blank');return;
    }


    if(type=='uklh_1'){
        var url		= "4px/OmsIndex.php?type=create&bill="+bill;
        window.open(url,'_blank');return;
    }
    if(type=='uklh_2'){
        var url		= "4px/OmsIndex.php?type=gettrack&bill="+bill;
        window.open(url,'_blank');return;
    }
    if(type=='uklh_3'){
        var url		= "4px/OmsIndex.php?type=outstore&bill="+bill;
        window.open(url,'_blank');return;
    }

    if(type=='uklh_4'){
        var url		= "4px/OmsIndex.php?type=create&bill="+bill+"&remotearea=1";
        window.open(url,'_blank');return;
    }

    if(undefined!=typeArrYKD[type]){
        var url		= "/t.php?s=/Transport/Ykd/batchOrderAction&ordersn="+bill+"&type="+typeArrYKD[type];
        window.open(url,'_blank');
    }
    if(undefined!=typeArrSY[type]){
        var url		= "/t.php?s=/Transport/ShunYou/batchOrderAction&ordersn="+bill+"&type="+typeArrSY[type];
        window.open(url,'_blank');
    }

}





function pxsubmitv2(type){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    if(type=='print'){
        var url		= "4px/4pxlocalprint.php?type=creat&bill="+bill;
        var url1  = "toscan.php?bill="+bill;
        window.open(url1,"_blank");
        window.open(url,"_blank");
    }else{
        var url		= "orderonline_testv2.php?type=creat&bill="+bill;
        openwindow(url,'',550,385);
    }

}

function stsubmit(type){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    if(type == 'creat'){
      var url		= "Sfcaddorder.php?bill="+bill+"&type=creat";
    }else if(type == 'print'){
      var url1 = "./toscan.php?bill="+bill;
      var url		= "Sfcaddorder.php?bill="+bill+"&type=print";
    }
    if(url1) {
      window.open(url1, '_blank');
    }
    window.open(url,"_blank");

}

function jmssubmit(type){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    if(type == 'creat'){
      var url		= "Jmsaddorder.php?bill="+bill+"&type=creat";
    }else if(type == 'print'){
      var url1 = "./toscan.php?bill="+bill;
      var url		= "Jmsaddorder.php?bill="+bill+"&type=print";
    }
    if(url1) {
      window.open(url1, '_blank');
    }
    window.open(url,"_blank");

}

function ostsubmit(type){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    if(type == 'creat'){
      var url		= "ostaddorder.php?bill="+bill+"&type=creat";
    }else if(type == 'print'){
      var url1 = "./toscan.php?bill="+bill;
      var url		= "ostaddorder.php?bill="+bill+"&type=print";
    }else if(type == 'reGetLabel'){
      var url		= "ostaddorder.php?bill="+bill+"&type=reGetLabel";
    }
    if(url1) {
      window.open(url1, '_blank');
    }
    window.open(url,"_blank");

}

function dgsubmit(type){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    if(type == 'creat'){
        var url		= "dgaddorder.php?bill="+bill+"&type=creat";
    }else if(type == 'print'){
        var url1 = "./toscan.php?bill="+bill;
        var url		= "dgaddorder.php?bill="+bill+"&type=print";
    }else if(type == 'del'){
        var url		= "dgaddorder.php?bill="+bill+"&type=del";
    }
    if(url1) {
        window.open(url1, '_blank');
    }
    window.open(url,"_blank");
}

function ajsubmit(type){
    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }
    if(type == 'creat'){
        var url		= "ajaddorder.php?bill="+bill+"&type=creat";
    }else if(type == 'print'){
        var url1 = "./toscan.php?bill="+bill;
        var url		= "ajaddorder.php?bill="+bill+"&type=print";
    }
    if(url1) {
        window.open(url1, '_blank');
    }
    window.open(url,"_blank");
}

function ebaymarketsmt(type){


    var bill	= getSelectOrder();
    if(bill == ""){
        alert("请选择订单号");
        return false;
    }

    var alerstr		= '您确认将选中订单在 Aliexpress 上标记发出吗?';

    if(type == 1){
        alerstr		= '你确认出库吗';
    }
    if(confirm(alerstr)){
        window.open("ordermarketsmt.php?ordersn="+bill+"&type="+type,"_blank");
    }
}

function viewProduct(sku){
    var url="productindex.php?module=warehouse&action=货品资料管理&keys="+sku+"&searchs=1&view=1";
    window.open(url,"_blank")
}


/* ############################################################################################# */
/* ############################################################################################# */

    function cdeub_submit(typevalue)
    {
        var bill=getSelectOrder();
        if(bill == ""){
            alert("请选择订单号");
            return false;
        }
        var url ='';
        if(typevalue == 'cdeub_print'){
            url = 'alipost/cdeub_index.php?action=print&bill='+bill;
        }else if(typevalue == 'cdeub_submit'){
            url = 'alipost/cdeub_index.php?action=submit&bill='+bill;
        }else if(typevalue == 'cdeub_mark_deliver'){
            url = 'alipost/cdeub_index.php?action=mark_deliver&bill='+bill;
        }
        window.open(url , '_blank');


    }

/* ############################################################################################# */
/* ############################################################################################# */


    function show_combine_details(combines)
    {
        var title="查看合并订单合并信息";
        funsTool.showBodyMask();
        funsTool.showModbox(title,400,1100,function(){
                $.post(
                    './toxls/ajax.php',
                    {combines: combines, doaction: 'ajaxViewCombine'},
                    function (re) {
                        var table_html = '<div class="dataItem"><table class="dataTable"><tr style="border-bottom: 1px solid #555">' +
                            '<th>订单编号</th><th>平台订单号</th><th>SKU * 数量</th> <th>Record编号</th><th>ItemId</th><th>添加到erp时间</th><th>付款时间</th><th>ebay用户名</th></tr>';
                        if(re.status){
                            var dataLength = re.data.length;
                            console.log(re.data);
                            console.log('长度 : '+dataLength);
                            for(var i=0 ; i<dataLength ; i++){
                                table_html +=
                                    '<tr> <td>'+re.data[i].ebay_id+'</td>' +
                                    '<td>'+re.data[i].ebay_ordersn+'</td>'+
//                                    '<td>'+re.data[i].sku+' ## '+re.data[i].recordnumber+'</td>'+
                                    '<td colspan="3">'+array_dump( re.data[i].sku , re.data[i].recordnumber , re.data[i].ebay_amount , re.data[i].ebay_itemid )+'</td>'+
//                                    '<td>'+re.data[i].counts+'</td>'+
//                                    '<td>'+re.data[i].ebay_itemid+'</td>'+
                                    ' <td>'+re.data[i].ebay_addtime+'</td> '+
                                    ' <td>'+re.data[i].ebay_paidtime+'</td> '+
                                    ' <td>'+re.data[i].ebay_username+'</td> </tr>';
                            }
                            funsTool.insertModBox(table_html +'</table></div>');
                        }
                    },
                    'json'
                )}
            , 1);
    }

    function __close(){
        funsTool.deleteModBox(1);
    }

    function getSelectOrder(){
        var bill='';
        //var checkboxs = document.getElementsByName("ordersn");
        //var checkboxs = $('#orderList .ebay_id')[0];
        var checkboxs = getElementsClass('ebay_id');
        for(var i=0;i<checkboxs.length;i++){
            if(checkboxs[i].checked == true){
                bill = bill + ","+checkboxs[i].value;
            }
        }
        return bill.replace(/^,*|,*$/g,"");
    }

    function getElementsClass(classnames){ 
        var classobj= new Array();//定义数组 
        var classint=0;//定义数组的下标 
        var tags=document.getElementsByTagName("*");//获取HTML的所有标签 
        for(var i in tags){//对标签进行遍历 
            if(tags[i].nodeType==1){//判断节点类型 
                if(tags[i].getAttribute("class") == classnames){ //判断和需要CLASS名字相同的，并组成一个数组 
                    classobj[classint]=tags[i]; 
                    classint++; 
                }
            } 
        } 
        return classobj;//返回组成的数组 
    } 

    function array_dump(arr1 , arr2 , arr3 , arr4){
        var arrLength = arr1.length;
        var Str = '<table border="1" style="text-align:center;border:1px solid #333;width:100%">';
        for(var j=0 ; j<arrLength ; j++){
            Str += '<tr><td>'+arr1[j]+' × '+arr3[j]+'</td><td>'+arr2[j]+"</td><td>"+arr4[j]+'</td></tr>';
        }
        return Str+'</table>';
    }

    function showProfit(ebayid){
        funsTool.showModbox('查看订单利润明细',300,800,function(){});
        $.post(
            './toxls/ajax.php',
            {ebayid: ebayid, doaction: 'ajaxViewProfit'},
            function(data){
                funsTool.insertModBox(data,true);
            }
        );
    }

    function openOrderDetail(ordersn){
        var url='./t.php?s=/Order/Orderindex/ordermodifive&open=2,2,2,2&ordersn='+ordersn;
        layer.open({
            type: 2,
            title: '订单信息',
            shadeClose: true,
            shade: 0.8,
            area: ['80%', '80%'],
            content: url
        });
    }

    //复制订单new
    function copyOrder(ebay_id, ordersn){
        var html = '<div><style>.copy_info{width:130px;display:inline-block;}.layui-layer-content{height:auto !important;}</style>';
        html += '<p style="padding:0 0 5px 0;font-size:14px;"><span class="copy_info">原订单号：</span>' + ebay_id + '</p>';
        html += '<p style="padding:0 0 5px 0;font-size:14px;"><span class="copy_info">复制后的订单类型：</span>';
        html += '<select name="ordertype" class="ordertype select-base">';

        $.ajax({
            type : 'post',
            url  : './t.php?s=/Order/CopyOrder/getOrderType',
            async : false,
            success : function (xhr){
                json = $.parseJSON(xhr);
                $.each(json.info, function(index, value){
                    html += '<option value="'+value+'">'+value+'</option> ';
                });
            }
        });
/*
        html += '<option value="线下订单">线下订单</option> ';
        html += '<option value="手工订单">手工订单</option> ';
        html += '<option value="补发订单">补发订单</option> ';
*/
        html += '</select></p>';
        html += '<p style="padding:0 0 5px 0;font-size:14px;"><span class="copy_info">复制后的订单状态：</span>';
        html += '<select name="orderstatus" class="orderstatus select-baset">';
        html += '<option value="0">待付款订单</option> ';
        html += '<option value="1">待处理订单</option> ';
        html += '</select></p>';
        html += '<p style="padding: 0;font-size:14px;"><span class="copy_info">复制订单原因：</span>';
        html += '<textarea name="copy_reason" class="copy_reason" cols="30" rows="2"></textarea>';
        html += '</p>';
        html += '<p class="msg" style="color:red;"></p>';
        html += '</div>';

        url = "/t.php?s=/Order/CopyOrder/copy";
        layer.open({
            title : '复制订单',
            content: html,
            area : ['450px','300px'],
            btn : ['确认复制','取消'],
            yes: function(index, layero){
                ordertype = $.trim($(layero).find('.ordertype').val());
                orderstatus = $.trim($(layero).find('.orderstatus').val());
                copy_reason = $.trim($(layero).find('.copy_reason').val());

                if(!ordertype || ordertype == ''){
                    $(layero).find('.msg').text('请选择订单类型！');
                    return false;
                }
                if(orderstatus == '' && orderstatus != 0){
                    $(layero).find('.msg').text('请选择订单状态！');
                    return false;
                }
                if(!copy_reason || copy_reason == ''){
                    $(layero).find('.msg').text('请填写复制订单原因！');
                    return false;
                }

                layer.close(index);
                var loading = layer.load();
                $.ajax({
                    url  : url,
                    type : 'post',
                    data : {
                        ebay_id : ebay_id,
                        ordersn : ordersn,
                        ordertype : ordertype,
                        orderstatus : orderstatus,
                        copy_reason : copy_reason,
                    },
                    success : function(xhr){
                        layer.close(loading);
                        var json = $.parseJSON(xhr);

                        if(json.status == 1){
                            //显示复制成功，查看订单详情按钮
                            layer.open({
                                title : '订单复制结果',
                                content: json.info,
                                area : ['380px','230px'],
                                btn : ['查看订单详情','关闭'],
                                yes: function(index, layero){
                                    layer.close(index);
                                    openOrderDetail(json.ordersn);
                                },
                                cancle: function(index, layero){
                                    layer.close(index);
                                }
                            });
                        }else{
                            layer.msg(json.info, {icon:2});
                        }
                    }
                });
            },
            cancle: function(index, layero){
                layer.close(index);
            }
        }); 
    }

    //创建订单
    function createOrder(){
        var url='./t.php?s=/Order/CreateOrder/showCreate';
        layer.open({
            type: 2,
            title: '创建订单',
            shadeClose: false,
            scrollbar: false,
            shade: 0.8,
            area: ['80%', '80%'],
            content: url
        });
    }

/*
//有问题订单显示数量标识
var issue_order_flag = <?php echo (isset($_REQUEST['ostatus']) && $_REQUEST['ostatus'] == 1728) ? 1 : 0; ?>;
//ajax获取数量
if(issue_order_flag == 1){
    $.ajax({
        url : 'toxls/ajax.php',
        type : 'post',
        data : {
            doaction : 'ajaxgetErrorstatuscount',
        },
        success : function (xhr){
            var json = $.parseJSON(xhr);
            var all_option = $('#processed_status option');
            $.each(all_option, function(index, value){
                var key = $(value).val();
                if(key == '') return true;  //跳过值为空的
                if(json[key] !== undefined){
                    $(value).text($(value).text() + '(' + json[key] + ')');
                }else{
                    $(value).text($(value).text() + '(0)');
                }
            });
        }
    });
}
*/

//选择物流
function select_ship(that, type){
    var text = $(that).text();
    var bill = getSelectOrder();
    if(bill == ''){
        layer.msg('请先选择订单！', {icon:2});
        return false;
    }
    if(!type){
        layer.msg('类型不正确！请检查！', {icon:2});
        return false;
    }
    var url = ''
    if(type == 'inland'){   //国内
        url = '/t.php?s=/Order/OrderManage/inlandShipList';
    }
    if(type == 'foreign'){  //国外
        url = '/t.php?s=/Order/OrderManage/foreignShipList';
    }
    layer.open({
        type: 2,
        title: text,
        shadeClose: false,  //点击遮罩关闭
        scrollbar: false,   //禁止父页面滚动
        shade: 0.3, //遮罩透明度
        maxmin: false, //开启最大化最小化按钮
        area: ['900px', '80%'],
        content: url   //链接
    });
}

