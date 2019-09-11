/**
 * 扫描二维码修改
 * @author Shawn
 * @date 2018-09-05
 * @param event
 * @param self
 * @returns {boolean}
 */
function changShowSku(event,self) {
    var id = $(self).attr("id");
    var sku = $.trim (document.getElementById (id).value);
    if (sku == "") return;

    var isie = (document.all) ? true : false;//判断是IE内核还是Mozilla
    var keyCode;
    if (isie) {
        keyCode = window.event.keyCode;//IE使用windows.event事件
    } else {
        keyCode = event.which;//3个按键函数有一个默认的隐藏变量，这里用e来传递。e.which给出一个索引值给Mo内核（注释1）
    }
    if (keyCode != 13) {
        return false;
    }
    if(sku.indexOf("$") >= 0){
        //禁止form提交
        event.cancleBubble = true;
        event.returnValue = false;
        var skuStr = sku.split("$");
        sku = skuStr[1];
        document.getElementById(id).value = sku;
        event.returnValue = true;
        return true;
    }
}