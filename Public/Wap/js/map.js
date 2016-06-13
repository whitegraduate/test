/**
 * 获取用户当前位置信息
 * @param fun 需要执行的函数
 */
function get_location(fun){
    var geolocation = new BMap.Geolocation();
    geolocation.getCurrentPosition(function(r){
        if(this.getStatus() == BMAP_STATUS_SUCCESS){
            fun(r);
        }
        else{
            alert('failed:'+this.getStatus());
        }
    },{enableHighAccuracy: true});
}

//将门店以标注样式加入到地图中
function addMarker(arr) {
    var markerIcon = new BMap.Icon(MapIcon1, new BMap.Size(262/7,262/7));
    markerIcon.setImageSize(new BMap.Size(262/7,262/7));
    for(var i =0 ; i < arr.length; i ++) {
        var point = new BMap.Point(arr[i].longitude,arr[i].latitude);
        var marker = new BMap.Marker(point,{icon:markerIcon});  // 创建标注
        map.addOverlay(marker);
        marker.setAnimation(BMAP_ANIMATION_BOUNCE);
        marker.id = arr[i].id;
        marker.title = arr[i].name;
        marker.point = point;
        if('' == arr[i].mobile)
            marker.mobile = arr[i].tel;
        else
            marker.mobile = arr[i].mobile;
        marker.address = arr[i].address;
        marker.addEventListener("click", function(){
            var opts = {
                width : 200,     // 信息窗口宽度
                height: 150,     // 信息窗口高度
                title : this.title, // 信息窗口标题
                enableMessage:true,//设置允许信息窗发送短息
                message:"戳下面的链接看下地址喔~"
            }
            var url = MapUrl + this.id;

            var info = "<div style=\"font-size:12px; margin:10px 10px 0 10px;\">"
                +"<div style=\"float:left;\">星级：★</div>"
                +"<div style=\"float:right;\">订单数：0</div>"
                +"<div style=\"clear:both;\"></div>"
                +"</div>"
                +"<div style=\"font-size:12px; margin:10px 10px 0 10px;\">地址："+this.address+"</div>"
                +"<div style=\"font-size:12px; margin:10px 10px 0 10px;\">电话："+this.mobile+"</div>"
                +"<div style=\"width:80%; margin:0 auto; height:30px; background:#00af89; font-size:12px; line-height:30px; text-align:center; margin-top:15px;\">"
                +"<a href=\""+url+"\" style=\"color:#fff; text-decoration:none;\">点击查看门店详情信息</a></div>";

            infoWindow = new BMap.InfoWindow(info, opts);  // 创建信息窗口对象
            map.openInfoWindow(infoWindow,this.point); //开启信息窗口
        });
    }
}

/**
 * 坐标转换
 */
(function(){        //闭包
    function load_script(xyUrl, callback){
        var head = document.getElementsByTagName('head')[0];
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = xyUrl;
        //借鉴了jQuery的script跨域方法
        script.onload = script.onreadystatechange = function(){
            if((!this.readyState || this.readyState === "loaded" || this.readyState === "complete")){
                callback && callback();
                // Handle memory leak in IE
                script.onload = script.onreadystatechange = null;
                if ( head && script.parentNode ) {
                    head.removeChild( script );
                }
            }
        };
        // Use insertBefore instead of appendChild  to circumvent an IE6 bug.
        head.insertBefore( script, head.firstChild );
    }
    function translate(point,type,callback){
        var callbackName = 'cbk_' + Math.round(Math.random() * 10000);    //随机函数名
        var xyUrl = "http://api.map.baidu.com/ag/coord/convert?from="+ type + "&to=4&x=" + point.lng + "&y=" + point.lat + "&callback=BMap.Convertor." + callbackName;
        //动态创建script标签
        load_script(xyUrl);
        BMap.Convertor[callbackName] = function(xyResult){
            delete BMap.Convertor[callbackName];    //调用完需要删除改函数
            var point = new BMap.Point(xyResult.x, xyResult.y);
            callback && callback(point);
        }
    }

    window.BMap = window.BMap || {};
    BMap.Convertor = {};
    BMap.Convertor.translate = translate;
})();

/**
 * 城市服务处理
 */
function loc_ser_now(){
    get_location(function(k){
        $.post("/wap.php/Location/index/from/x",{"city": k.address.city,"province": k.address.province},function(page){
            if(page){
                chose_city(page,k);
            }
        });
    });
}

function chose_city(pageinfo,gps){
    var pagei = layer.open({
        content: pageinfo,
        success: function(olayer){
            var cla = 'getElementsByClassName';
            if(olayer[cla]('yes').length){
                olayer[cla]('yes')[0].onclick = function(){
                    layer.close(pagei);
                    $.post("/wap.php/Location/setCity/from/x",{"city": gps.address.city,"province": gps.address.province},function(page){
                        var message = layer.open({content:page.info,time:2});
                    },"json");
                };
            }
            if(olayer[cla]('closed').length){
                olayer[cla]('closed')[0].onclick = function(){
                layer.close(pagei)
            };}
            if(olayer[cla]('other').length){
                olayer[cla]('other')[0].onclick = function(){
                    layer.close(pagei);
                    $.get("/wap.php/Location/moreCityLink/from/x",function(page){
                        more_city(page);
                    });
                }
            }
            if(olayer[cla]('city').length){
                for(var i =0;i<olayer[cla]('city').length;i++){
                    olayer[cla]('city')[i].onclick = function(){
                        $.post("/wap.php/Location/setCityid/from/x",{"cityid": $(this).attr("id")},function(page){
                            var message = layer.open({content:page.info,time:2});
                        },"json");
                    };
                }
            }
        }
    });
}


function more_city(page_info){
    var page_more = layer.open({
        content: page_info,
        success: function(pa){
            var cla = 'getElementsByClassName';
            for(var i =0;i<pa[cla]('city').length;i++){
                pa[cla]('city')[i].onclick = function(){
                    $.post("/wap.php/Location/setCityid/from/x",{"cityid": $(this).attr("id")},function(page){
                        var message = layer.open({content:page.info,time:2});
                    },"json");
                };
            }
        }
    });
}