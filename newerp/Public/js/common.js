/**
 * 全选反选
 */
function checkAllcheckbox(obj) {
    if ($(obj).is(':checked')) {
        $(":checkbox").prop("checked", true);
    } else {
        $(":checkbox").prop("checked", false);
    }
}