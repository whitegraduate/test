/**
 * 相关函数
 * Author：Ketory
 */
function galert(msg)
{
    var time = arguments[1] ? arguments[1] : 2;
    layer.open({
        content: msg,
        style: 'background-color:#00af89; color:#fff; border:none;',
        time: time
    });
}
function drop_list(list,chose){
    $(list).each(function(){
        var s=$(this);
        var z=parseInt(s.css("z-index"));
        var dt=$(this).children("dt");
        var dd=$(this).children("dd");
        var _show=function(){dd.slideDown(200);s.css("z-index",z+1);};   //展开效果
        var _hide=function(){dd.slideUp(200);s.css("z-index",z);};    //关闭效果
        dt.click(function(){dd.is(":hidden")?_show():_hide();});
        dd.find("a").click(function(){
            dt.html($(this).html());
            $(chose).val($(this).html());_hide();
        });     //选择效果（如需要传值，可自定义参数，在此处返回对应的“value”值 ）
        $("body").click(function(i){ !$(i.target).parents(".select").first().is(s) ? _hide():"";});
    });
}
function slshop(a,b,c,d,e,f){
    $(a).each(function(){
        var s=$(this);
        var z=parseInt(s.css("z-index"));
        var dt=$(this).children("dt");
        var dd=$(this).children("dd");
        var _show=function(){dd.slideDown(200);dt.addClass("cur");s.css("z-index",z+1);};   //展开效果
        var _hide=function(){dd.slideUp(200);dt.removeClass("cur");s.css("z-index",z);};    //关闭效果
        dt.click(function(){dd.is(":hidden")?_show():_hide();});
        dd.find("a").click(function(){dt.html($(this).html());

            $(b).val($(this).attr("data"));
            $(c).show();
            $(d).html('电话：'+$(this).attr('tel'));
            $(e).html($(this).attr('addr'));
            $(f).attr('href','/wap.php/Index/route/code/MD/id/'+$(this).attr("data")+'.html');

            _hide();});     //选择效果（如需要传值，可自定义参数，在此处返回对应的“value”值 ）
        $("body").click(function(i){ !$(i.target).parents(".select").first().is(s) ? _hide():"";});

    });
}
function validatemobile(mobile)
{
    var myreg = /^((\(\d{2,3}\))|(\d{3}\-))?(13|15|17|18|14)\d{9}$/;
    if(!myreg.exec(mobile))
    {
        return false;
    }
    return true;
}


