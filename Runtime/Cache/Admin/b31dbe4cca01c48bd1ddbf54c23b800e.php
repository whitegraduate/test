<?php if (!defined('THINK_PATH')) exit();?><input type="file" id="upload_picture_<?php echo ($addons_data["name"]); ?>" >
<input type="hidden" name="<?php echo ($addons_data["name"]); ?>" id="<?php echo ($addons_data["name"]); ?>" value="<?php echo ($addons_data["value"]); ?>"/>
<div class="upload-img-box">
    <?php if(!empty($addons_data["value"])): ?><div class="upload-pre-item"><img src="<?php echo ($addons_data["value"]); ?>"/></div><?php endif; ?>
</div>
<script type="text/javascript">
    //上传图片
    /* 初始化上传插件 */
    $("#upload_picture_<?php echo ($addons_data["name"]); ?>").uploadify({
        "height": '<?php echo ($addons_config["btn_height"]); ?>',
        "swf": "/Public/static/uploadify/uploadify.swf",
        "fileObjName": "download",
        "buttonText": "上传图片",
        "uploader": "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
        "width": '<?php echo ($addons_config["btn_width"]); ?>',
        'removeTimeout': 1,
        'fileTypeExts': '<?php echo ($addons_config["filetype_exts"]); ?>',
        "onUploadSuccess": upload_<?php echo ($addons_data["name"]); ?>,
        'onFallback': function () {
            alert('未检测到兼容版本的Flash.');
        }
    });

    function upload_<?php echo ($addons_data["name"]); ?>(file, data){
        var jsondata = $.parseJSON(data);
        var src = '';
        if(jsondata.status){
            src = jsondata.url || '' + jsondata.path;
            $("#<?php echo ($addons_data["name"]); ?>").val(src);
            $("#<?php echo ($addons_data["name"]); ?>").parent().find('.upload-img-box').html(
                    '<div class="upload-pre-item"><img src="' + src + '"/></div>'
            );
        } else {
            updateAlert(jsondata.info);
            setTimeout(function(){
                $('#top-alert').find('button').click();
                $(that).removeClass('disabled').prop('disabled',false);
            },1500);
        }
    }
</script>