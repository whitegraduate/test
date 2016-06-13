<?php if (!defined('THINK_PATH')) exit();?><div class="form-item">
    <label class="item-label">省市区</label>
    <div class="controls">
        省市
        <select name="cityid" class="cityid">
            <option value="-1">请选择</option>
            <?php if(is_array($citylist)): $i = 0; $__LIST__ = $citylist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["province"]); ?>-<?php echo ($vo["city"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
        区域
        <select name="areaid" class="areaid">
            <option value="-1">请选择</option>
        </select>
    </div>
</div>

<script type="text/javascript">
    $("select[name='cityid']").change(function () {
        var a = $(this).val();
        console.log(a);

        $.get("<?php echo addons_url('Ssq://Ssq/get_area');?>", {cityid: a},
                function (data) {
                    if(data!=null)
                    {
                        var str ='<option value="-1">请选择</option>';
                        for(var i=0;i<data.length;i++)
                        {
                            str+="<option value="+data[i].id+">"+data[i].areaname+"</option>";
                        }
                        $("select[name='areaid']").html(str);

                        var a = '<?php echo ($areaid); ?>';
                        if(a>0)
                        {
                            $("select[name='areaid']").val(a);
                        }
                    }
                    else
                    {
                        $("select[name='areaid']").html('<option value="-1">请选择</option>');
                    }
                });
    });

    $(document).ready(function(){
        var c ='<?php echo ($cityid); ?>';
        if(c>0)
        {
            $("select[name='cityid']").val(c);
            $("select[name='cityid']").change();
        }
    });
</script>