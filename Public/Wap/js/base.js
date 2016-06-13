document.write("<script language='javascript' src='/Public/Wap/js/layer.m/layer.m.js'></script>");
function lalert(msg)
{
    var time = arguments[1] ? arguments[1] : 2;
    layer.open({
        content: msg,
        style: 'background-color:#ff4f51; color:#fff; border:none;',
        time: time
    });
}

function lalert_url(msg, url)
{
    layer.open({
        content: msg,
        btn: ['确认'],
        shadeClose: false,
        yes: function () {
            top.location.href = url;
        }, no: function () {

        }
    });
}

function lconfirm_submit(msg, formid)
{
    layer.open({
        content: msg,
        btn: ['确认', '取消'],
        shadeClose: false,
        yes: function () {
            $("#" + formid).submit();
        }, no: function () {
        }
    });
}

function lconfirm_ajax(msg, url, id)
{
    layer.open({
        content: msg,
        btn: ['确认', '取消'],
        shadeClose: false,
        yes: function () {
            $.ajax({
                type: "GET",
                url: url,
                dataType: 'json',
                async: false,
                success: function (data) {
                    if ('1' == data)
                        galert('操作成功！');
                    else
                        galert('操作失败');
                }
            });

        }, no: function () {
        }
    });
}

function lcontent(msg)
{
    layer.open({
        type: 1,
        content: msg,
        style: 'width:80%;height:400px;padding:10px;overflow:scroll;text-indent:2em;'
    });
}

function lloading(msg)
{
    layer.open({
        type: 2,
        content: msg
    });
}

/**
 * 获取页面参数值
 */
function request(paras) {
    var url = location.href;
    var paraString = url.substring(url.indexOf("?") + 1, url.length).split("&");
    var paraObj = {}
    for (i = 0; j = paraString[i]; i++) {
        paraObj[j.substring(0, j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=") + 1, j.length);
    }
    var returnValue = paraObj[paras.toLowerCase()];
    if (typeof (returnValue) == "undefined") {
        return "";
    } else {
        return returnValue;
    }
}