<?php defined('IN_IA') or exit('Access Denied');?><!-- 
 * 七牛云视频管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
-->
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<link href="<?php echo MODULE_URL;?>template/web/style/fycommon.css" rel="stylesheet">
<style type="text/css">
@-webkit-keyframes rotation{
	from {-webkit-transform: rotate(0deg);}
	to {-webkit-transform: rotate(360deg);}
}
.Rotation{
	-webkit-transform: rotate(360deg);
	animation: rotation 3s linear infinite;
	-moz-animation: rotation 3s linear infinite;
	-webkit-animation: rotation 3s linear infinite;
	-o-animation: rotation 3s linear infinite;
}
</style>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/video/video-header', TEMPLATE_INCLUDEPATH)) : (include template('web/video/video-header', TEMPLATE_INCLUDEPATH));?>

<?php  if($operation == 'display') { ?>
<div class="main">
    <div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="fy_lessonv2" />
                <input type="hidden" name="do" value="video" />
                <input type="hidden" name="op" value="display" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">视频名称</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="keyword" type="text" value="<?php  echo $_GPC['keyword'];?>">
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">上传时间</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <?php echo tpl_form_field_daterange('time', array('starttime'=>($starttime ? date('Y-m-d', $starttime) : false),'endtime'=> ($endtime ? date('Y-m-d', $endtime) : false)));?>
                    </div>
                    <div class="col-sm-3 col-lg-3">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
						&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('video',array('op'=>'upqiniu'));?>" class="btn btn-success"><i class="fa fa-plus"></i> 上传音视频</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="panel panel-default">
        <form action="" method="post" class="form-horizontal form" >
        <div class="table-responsive panel-body">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th style="width:15%;">视频预览</th>
                    <th style="width:15%;">视频名称</th>
                    <th style="width:10%;">视频大小</th>
					<th style="width:14%;">上传时间</th>
                    <th>文件链接</th>
					<th style="text-align:right;width:8%;">操作</th>
                </tr>
                </thead>
                <tbody style="font-size: 13px;">
                <?php  if(is_array($list)) { foreach($list as $key => $item) { ?>
                <tr>
                    <td>
						<a href="<?php  echo $this->createWebUrl('video', array('op'=>'qiniuPreview','id'=>$item['id']));?>"><img src="<?php echo MODULE_URL;?>template/mobile/images/videoCover.png?v=1" width="150"/></a>
					</td>
                    <td style="word-break:break-all;"><?php  echo $item['name'];?></td>
					<td><?php echo round(($item['size']/1024)/1024,2)?round(($item['size']/1024)/1024,2):0.01;?> MB</td>
					<td><?php  echo date('Y-m-d H:i:s', $item['addtime'])?></td>
                    <td>
                        <textarea style="width:300px;height:85px; border-radius:5px;" id="content<?php  echo $key;?>" style="overflow-y:auto;" onclick="selectTxt(this)"><?php  echo $item['qiniu_url'];?></textarea>
                    </td>
					<td style="text-align:right;">
                        <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('video', array('op'=>'delQiniu', 'id'=>$item['id']));?>" title="删除" onclick="return confirm('确认删除？');return false;"><i class="fa fa-remove"></i></a>
                    </td>
                </tr>
                <?php  } } ?>
                </tbody>
            </table>
            <?php  echo $pager;?>
        </div>
    </div>
    </form>
</div>
<script type="text/javascript">
function videoContro(obj, type){
	var myvideo = document.getElementById(obj.id);
	if(document.getElementById(obj.id).paused){
		document.getElementById(obj.id).play();
	}else{
		document.getElementById(obj.id).pause();
	}
}
function selectTxt(obj){
	$(obj).select();
}
</script>

<?php  } else if($op=='upqiniu') { ?>
<link rel="stylesheet" href="<?php echo MODULE_URL;?>template/web/style/Qiniu/main.css">
<link rel="stylesheet" href="<?php echo MODULE_URL;?>template/web/style/Qiniu/highlight.css">

<div class="main">
	<div class="alert alert-info">
	    上传视频到七牛云对象存储，建议音频请上传<span style="color:red;">mp3</span>格式文件，视频请上传<span style="color:red;">H.264编码的mp4</span>格式文件
	</div>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane fade in active" id="demo" aria-labelledby="demo-tab">
			<div class="row" style="margin-top: 20px;">
				<input type="hidden" id="domain" value="<?php  echo $qiniu['url'];?>">
				<input type="hidden" id="uptoken_url" value="uptoken">
				<div class="col-md-12">
					<div id="container" style="position: relative;">
						<input type="file" id="pickfiles" class="btn btn-default" name="video">
					</div>
				</div>
				<div style="display:none" id="success" class="col-md-12">
					<div class="alert-success">
						队列全部文件处理完毕
					</div>
				</div>
				<div class="col-md-12 ">
					<table class="table table-striped table-hover text-left" style="margin-top:40px;display:none">
						<thead>
							<tr>
								<th class="col-md-4">文件名称</th>
								<th class="col-md-2">文件大小</th>
								<th class="col-md-6">上传详情</th>
							</tr>
						</thead>
						<tbody id="fsUploadProgress">
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane fade" id="code" aria-labelledby="code-tab">
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/Qiniu/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/Qiniu/moxie.js"></script>
<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/Qiniu/plupload.full.min.js"></script>
<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/Qiniu/zh_CN.js"></script>
<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/Qiniu/ui.js?v=2"></script>
<?php  if($qiniu['qiniu_area']==1) { ?>
<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/Qiniu/qiniu_huadong.js"></script>
<?php  } else if($qiniu['qiniu_area']==2) { ?>
<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/Qiniu/qiniu_huabei.js"></script>
<?php  } else if($qiniu['qiniu_area']==3) { ?>
<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/Qiniu/qiniu_huanan.js"></script>
<?php  } else if($qiniu['qiniu_area']==4) { ?>
<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/Qiniu/qiniu_beimei.js"></script>
<?php  } ?>
<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/Qiniu/highlight.js"></script>
<script type="text/javascript">hljs.initHighlightingOnLoad();</script>
<script type="text/javascript">
$(function() {
    var uploader = Qiniu.uploader({
        runtimes: 'html5,flash,html4',
        browse_button: 'pickfiles',
        container: 'container',
        drop_element: 'container',
        flash_swf_url: 'bower_components/plupload/js/Moxie.swf',
        dragdrop: true,
        chunk_size: '4mb',
		uptoken: "<?php  echo $token; ?>",
        multi_selection: !(mOxie.Env.OS.toLowerCase()==="ios"),
        filters : {
            max_file_size : "1024mb",
            prevent_duplicates: true,
            mime_types: [
                {title : "Audio files", extensions : "mp3"},
                {title : "Video files", extensions : "avi,mp4,wmv,rmvb,mov,mkv,flv"},
			]
        },
        domain: $('#domain').val(),
        get_new_uptoken: false,
        auto_start: true,
        log_level: 5,
        init: {
            'FilesAdded': function(up, files) {
                $('table').show();
                $('#success').hide();
                plupload.each(files, function(file) {
                    var progress = new FileProgress(file, 'fsUploadProgress');
                    progress.setStatus("等待...");
                    progress.bindUploadCancel(up);
                });
            },
            'BeforeUpload': function(up, file) {
                var progress = new FileProgress(file, 'fsUploadProgress');
                var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                if (up.runtime === 'html5' && chunk_size) {
                    progress.setChunkProgess(chunk_size);
                }
            },
            'UploadProgress': function(up, file) {
                var progress = new FileProgress(file, 'fsUploadProgress');
                var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                progress.setProgress(file.percent + "%", file.speed, chunk_size);
            },
            'UploadComplete': function() {
                $('#success').show();
            },
            'FileUploaded': function(up, file, info) {
                var progress = new FileProgress(file, 'fsUploadProgress');
                progress.setComplete(up, info);
				
            },
			'Key': function(up, file) {
				var key = "admin/"+file.name;

				$.ajax({
					url:"<?php  echo $this->createWebUrl('video', array('op'=>'saveQiniuUrl')); ?>",
					data:{name:file.name, com_name:key, size:file.size},
					type:'post',
					dataType:'json',
					success:function(msg){
					}
				});

				return key;
			},
            'Error': function(up, err, errTip) {
                $('table').show();
                var progress = new FileProgress(err.file, 'fsUploadProgress');
                progress.setError();
                progress.setStatus(errTip);
            }
        }
    });

    uploader.bind('FileUploaded', function() {
    });
    $('#container').on(
        'dragenter',
        function(e) {
            e.preventDefault();
            $('#container').addClass('draging');
            e.stopPropagation();
        }
    ).on('drop', function(e) {
        e.preventDefault();
        $('#container').removeClass('draging');
        e.stopPropagation();
    }).on('dragleave', function(e) {
        e.preventDefault();
        $('#container').removeClass('draging');
        e.stopPropagation();
    }).on('dragover', function(e) {
        e.preventDefault();
        $('#container').addClass('draging');
        e.stopPropagation();
    });



    $('#show_code').on('click', function() {
        $('#myModal-code').modal();
        $('pre code').each(function(i, e) {
            hljs.highlightBlock(e);
        });
    });


    $('body').on('click', 'table button.btn', function() {
        $(this).parents('tr').next().toggle();
    });


    var getRotate = function(url) {
        if (!url) {
            return 0;
        }
        var arr = url.split('/');
        for (var i = 0, len = arr.length; i < len; i++) {
            if (arr[i] === 'rotate') {
                return parseInt(arr[i + 1], 10);
            }
        }
        return 0;
    };

    $('#myModal-img .modal-body-footer').find('a').on('click', function() {
        var img = $('#myModal-img').find('.modal-body img');
        var key = img.data('key');
        var oldUrl = img.attr('src');
        var originHeight = parseInt(img.data('h'), 10);
        var fopArr = [];
        var rotate = getRotate(oldUrl);
        if (!$(this).hasClass('no-disable-click')) {
            $(this).addClass('disabled').siblings().removeClass('disabled');
            if ($(this).data('imagemogr') !== 'no-rotate') {
                fopArr.push({
                    'fop': 'imageMogr2',
                    'auto-orient': true,
                    'strip': true,
                    'rotate': rotate,
                    'format': 'png'
                });
            }
        } else {
            $(this).siblings().removeClass('disabled');
            var imageMogr = $(this).data('imagemogr');
            if (imageMogr === 'left') {
                rotate = rotate - 90 < 0 ? rotate + 270 : rotate - 90;
            } else if (imageMogr === 'right') {
                rotate = rotate + 90 > 360 ? rotate - 270 : rotate + 90;
            }
            fopArr.push({
                'fop': 'imageMogr2',
                'auto-orient': true,
                'strip': true,
                'rotate': rotate,
                'format': 'png'
            });
        }

        $('#myModal-img .modal-body-footer').find('a.disabled').each(function() {

            var watermark = $(this).data('watermark');
            var imageView = $(this).data('imageview');
            var imageMogr = $(this).data('imagemogr');

            if (watermark) {
                fopArr.push({
                    fop: 'watermark',
                    mode: 1,
                    image: 'http://www.b1.qiniudn.com/images/logo-2.png',
                    dissolve: 100,
                    gravity: watermark,
                    dx: 100,
                    dy: 100
                });
            }

            if (imageView) {
                var height;
                switch (imageView) {
                    case 'large':
                        height = originHeight;
                        break;
                    case 'middle':
                        height = originHeight * 0.5;
                        break;
                    case 'small':
                        height = originHeight * 0.1;
                        break;
                    default:
                        height = originHeight;
                        break;
                }
                fopArr.push({
                    fop: 'imageView2',
                    mode: 3,
                    h: parseInt(height, 10),
                    q: 100,
                    format: 'png'
                });
            }

            if (imageMogr === 'no-rotate') {
                fopArr.push({
                    'fop': 'imageMogr2',
                    'auto-orient': true,
                    'strip': true,
                    'rotate': 0,
                    'format': 'png'
                });
            }
        });

        var newUrl = Qiniu.pipeline(fopArr, key);

        var newImg = new Image();
        img.attr('src', '<?php echo MODULE_URL;?>template/mobile/images/loading.gif');
        newImg.onload = function() {
            img.attr('src', newUrl);
            img.parent('a').attr('href', newUrl);
        };
        newImg.src = newUrl;
        return false;
    });

});
</script>

<?php  } else if($op=='qiniuPreview') { ?>
<div class="main">
	<div class="panel panel-default">
		<div class="panel-heading">视频预览</div>
		<div class="panel-body" style="text-align: center;">
			<video src="<?php  echo $playurl;?>" controls="controls" width="640" height="360"></video>
		</div>
	</div>
</div>
<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>